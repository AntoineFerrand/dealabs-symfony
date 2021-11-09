<?php

namespace App\Controller;

use App\Entity\Deals;
use App\Entity\Vote;
use App\Event\VoteEvent;
use App\Listeners\BadgeListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    private $security;
    private $manager;
    private $dealsRepository;
    private $voteRepository;
    private $dispatcher;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->dealsRepository = $manager->getRepository(Deals::class);
        $this->voteRepository = $manager->getRepository(Vote::class);
        $this->dispatcher = new EventDispatcher();
        $listener = new BadgeListener($manager);
        $this->dispatcher->addListener('vote.voted', [$listener, 'onVoteAction']);
    }

    /**
     * @Route("/doVote/{id}/{type}/{route}", name="doVote")
     * @param Request $request
     * @return Response
     */
    public function doVote(Request $request): Response
    {
        $id = $request->get("id");
        $route = $request->get("route");
        $routeParams = $request->get("routeParams");
        $type = $request->get("type");
        if($this->security->getUser() == null) {
            return $this->redirectToRoute('error');
        }
        $deal = $this->dealsRepository->find($id);
        $account = $this->security->getUser();
        if(($type == "up" && !$this->voteRepository->canUpVote($deal, $account))
        || $type == "down" && !$this->voteRepository->canDownVote($deal, $account)) {
            if($routeParams) {
                return $this->redirectToRoute($route, $routeParams);
            } else {
                return $this->redirectToRoute($route);
            }
        }
        $pastVote = $this->voteRepository->findByAccoutDeal($account, $deal);
        $newVote = new Vote();
        $newVote->setIsPositive($type == "up");
        $newVote->setDeals($deal);
        $newVote->setAccount($account);

        if($pastVote != null) {
            $this->manager->remove($pastVote);
        } else {
            $this->dispatcher->dispatch(new VoteEvent($this->security->getUser()), VoteEvent::NAME);
        }
        $this->manager->persist($newVote);
        $this->manager->flush();
        $this->addFlash(
            'success',
            'Merci d\'avoir voté !'
        );
        if($routeParams) {
            return $this->redirectToRoute($route, $routeParams);
        } else {
            return $this->redirectToRoute($route);
        }
    }
}
