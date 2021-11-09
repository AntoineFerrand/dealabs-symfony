<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Event\DealCreatedEvent;
use App\Listeners\BadgeListener;
use App\Entity\Deals;
use App\Entity\Vote;
use App\Form\CodePromoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

class CodePromoController extends AbstractController
{
    private $security;
    private $dispatcher;
    private $dealsRepository;
    private $voteRepository;
    private $groupeRepository;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->dealsRepository = $manager->getRepository(Deals::class);
        $this->voteRepository = $manager->getRepository(Vote::class);
        $this->groupeRepository = $manager->getRepository(Groupe::class);
        $this->dispatcher = new EventDispatcher();
        $listener = new BadgeListener($manager);
        $this->dispatcher->addListener('deal.created', [$listener, 'onDealCreatedAction']);
    }

    /**
     * @Route("/create/code/promo", name="create_code_promo")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function createCodePromo(Request $request, EntityManagerInterface $manager): Response
    {

        $codePromo = new Deals();
        $codePromo->setType("code");
        $form = $this->createForm(CodePromoType::class, $codePromo);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $codePromo->setCreationDate( new \DateTime());
            if($this->security->getUser() == null) {
                return $this->redirectToRoute('error');
            }
            if($codePromo->getGroupes()->count() == 0) {
                $this->addFlash(
                    'error',
                    'Vous ne pouvez pas ajouter un code promo sans au moins un groupe !'
                );
            } else {
                $staticGroupes = $codePromo->getGroupes();
                $codePromo->removeAllGroupes();

                foreach($staticGroupes as $groupe) {
                    $groupe = $this->groupeRepository->find($groupe->getId());
                    $codePromo->addGroupe($groupe);
                }

                $codePromo->setShipping(0);
                $codePromo->setCreator($this->security->getUser());
                $this->dispatcher->dispatch(new DealCreatedEvent($this->security->getUser()), DealCreatedEvent::NAME);
                $manager->persist($codePromo);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Votre code promo a été ajouté'
                );
                unset($codePromo);
                unset($form);
                $codePromo = new Deals();
                $form = $this->createForm(CodePromoType::class, $codePromo);
            }
        }

        return $this->render('code_promo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/listing/code/promo", name="listing_code_promo")
     * @param Request $request
     * @return Response
     */
    public function listingCodePromo(Request $request): Response
    {
        $codes = $this->dealsRepository->findSortedCodesPromos();
        foreach($codes as $code) {
            $votes = $this->voteRepository->findVotesByDeals($code);
            if(sizeof($votes) > 0) {
                foreach($votes as $vote) {
                    if($vote->getIsPositive()) {
                        $code->upVote();
                    } else {
                        $code->downVote();
                    }
                }
            }
        }
        return $this->render('deals/listing.html.twig', [
            'deals' => $codes,
            'tabTitle' => "Nos codes promo",
            'pageTitle' => "Listing des codes promo (".sizeof($codes)." résultat(s))",
            'ext' => "base.html.twig"
        ]);
    }
}