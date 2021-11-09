<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Commentaire;
use App\Entity\Deals;
use App\Entity\Groupe;
use App\Entity\Vote;
use App\Event\CommentEvent;
use App\Event\DealCreatedEvent;
use App\Form\BonPlanType;
use App\Form\CommentaireType;
use App\Form\MailType;
use App\Listeners\BadgeListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

class BonPlanController extends AbstractController
{
    private $security;
    private $manager;
    private $dispatcher;
    private $dealsRepository;
    private $voteRepository;
    private $commentaireRepository;
    private $groupeRepository;
    private $accountRepository;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->manager = $entityManager;
        $listener = new BadgeListener($entityManager);
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addListener('comment.commented', [$listener, 'onCommentAction']);
        $this->dispatcher->addListener('deal.created', [$listener, 'onDealCreatedAction']);
        $this->dealsRepository = $entityManager->getRepository(Deals::class);
        $this->voteRepository = $entityManager->getRepository(Vote::class);
        $this->commentaireRepository = $entityManager->getRepository(Commentaire::class);
        $this->groupeRepository = $entityManager->getRepository(Groupe::class);
        $this->accountRepository = $entityManager->getRepository(Account::class);
    }

    /**
     * @Route("/create/bon/plan", name="create_bon_plan")
     * @param Request $request
     * @return Response
     */
    public function createBonPlan(Request $request): Response
    {
        $bonPlan = new Deals();
        $bonPlan->setType("deal");
        $form = $this->createForm(BonPlanType::class, $bonPlan);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($this->security->getUser() == null) {
                return $this->redirectToRoute('error');
            }
            if($bonPlan->getGroupes()->count() == 0) {
                $this->addFlash(
                    'error',
                    'Vous ne pouvez pas ajouter un bon plan sans au moins un groupe !'
                );
            } else {
                $staticGroupes = $bonPlan->getGroupes();
                $bonPlan->removeAllGroupes();
                foreach($staticGroupes as $groupe) {
                    $groupe = $this->groupeRepository->find($groupe->getId());
                    $bonPlan->addGroupe($groupe);
                }
                $bonPlan->setCreationDate( new \DateTime());
                $bonPlan->setCreator($this->security->getUser());
                $this->dispatcher->dispatch(new DealCreatedEvent($this->security->getUser()), DealCreatedEvent::NAME);
                $this->manager->persist($bonPlan);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    'Votre bon plan a été ajouté'
                );
                unset($bonPlan);
                unset($form);
                $bonPlan = new Deals();
                $form = $this->createForm(BonPlanType::class, $bonPlan);
            }
        }
        return $this->render('bon_plan/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/listing/bon/plan", name="listing_bon_plan")
     * @return Response
     */
    public function listingBonsPlans(): Response
    {
        $bonsPlans = $this->dealsRepository->findSortedBonsPlans();
        foreach($bonsPlans as $bonPlan) {
            $votes = $this->voteRepository->findVotesByDeals($bonPlan);
            if(sizeof($votes) > 0) {
                foreach ($votes as $vote) {
                    if ($vote->getIsPositive()) {
                        $bonPlan->upVote();
                    } else {
                        $bonPlan->downVote();
                    }
                }
            }
        }
        return $this->render('deals/listing.html.twig', [
            'deals' => $bonsPlans,
            'tabTitle' => "Nos bons plans",
            'pageTitle' => "Listing des bons plans (".sizeof($bonsPlans)." résultat(s))",
            'ext' => "base.html.twig"
        ]);
    }

    /**
     * @Route("/{id}/bon/plan", name="showBonPlan")
     * @return Response
     * @param String $id
     * @param Request $request
     */
    public function show(String $id, Request $request): Response
    {
        $bonPlan = $this->dealsRepository->find($id);
        $votes = $this->voteRepository->findVotesByDeals($bonPlan);
        if(sizeof($votes) > 0) {
            foreach ($votes as $vote) {
                if ($vote->getIsPositive()) {
                    $bonPlan->upVote();
                } else {
                    $bonPlan->downVote();
                }
            }
        }
        $comments = $this->commentaireRepository->findByDeal($bonPlan);
        $comment = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($this->security->getUser() == null) {
                return $this->redirectToRoute('error');
            }
            //$date = new \DateTime('now', new DateTimeZone('Europe/Paris'));
            $date = new \DateTime('now');
            $comment->setDate($date);
            $comment->setAccount($this->security->getUser());
            $comment->setDeal($bonPlan);
            $this->dispatcher->dispatch(new CommentEvent($this->security->getUser()), CommentEvent::NAME);
            $this->manager->persist($comment);
            $this->manager->flush();
            unset($comment);
            unset($form);
            $comment = new Commentaire();
            $form = $this->createForm(CommentaireType::class, $comment);
            $this->addFlash(
                'success',
                'Votre commentaire a bien été posté !'
            );
            $comments = $this->commentaireRepository->findByDeal($bonPlan);
        }
        return $this->render('bon_plan/show.html.twig', [
            'bonPlan' => $bonPlan,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/signal", name="signalBonPlan")
     * @param String $id
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function signal(String $id, Request $request, MailerInterface $mailer) : Response {
        $bonPlan = $this->dealsRepository->find($id);
        $users = $this->accountRepository->findAll();
        $form = $this->createForm(MailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            foreach ($users as $user){
                if (in_array("ROLE_ADMIN",$user->getRoles())) {
                    $content = $form->get('content')->getData();
                    $email = new TemplatedEmail();
                    $email->from(new Address("contact@mail.com", "Dealabs"))
                        ->to($user->getEmail())
                        ->htmlTemplate('bon_plan/email.html.twig')
                        ->context([
                            'bonplan' => $bonPlan,
                            'content' => $content
                        ])
                        ->subject("Un visiteur a signalé le produit suivant: ".$bonPlan->getTitle());
                    $mailer->send($email);
                    $this->addFlash(
                        'success',
                        'Ce bon plan a été signalé'
                    );
                    sleep(0.2);
                }
            }
        }
        return $this->render('bon_plan/signal.html.twig', [
            'bonPlan' => $bonPlan,
            'form' => $form->createView()
        ]);
    }
}