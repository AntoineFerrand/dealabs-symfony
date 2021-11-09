<?php

namespace App\Listeners;

use App\Controller\UserController;
use App\Entity\Account;
use App\Entity\Badge;
use App\Event\CommentEvent;
use App\Event\DealCreatedEvent;
use App\Repository\AccountRepository;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use App\Event\VoteEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class BadgeListener
{
    private $manager;
    private $accountRepository;
    private $badgeRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
        $this->accountRepository = $entityManager->getRepository(Account::class);
        $this->badgeRepository = $entityManager->getRepository(Badge::class);
    }

    public function onVoteAction(VoteEvent $event) {

        $event->getAccount()->setVoteCount($event->getAccount()->getVoteCount() + 1);
        if($event->getAccount()->getVoteCount() == 10) {
            $account = $this->accountRepository->find($event->getAccount()->getId());
            $badge = $this->badgeRepository->findVoteBadge();
            $account->addBadge($badge);
            $this->manager->persist($account);
            $this->manager->flush();
        }
    }

    public function onCommentAction(CommentEvent $event) {
        $event->getAccount()->setCommentCount($event->getAccount()->getCommentCount() + 1);
        if($event->getAccount()->getCommentCount() == 10) {
            $account = $this->accountRepository->find($event->getAccount()->getId());
            $badge = $this->badgeRepository->findCommentBadge();
            $account->addBadge($badge);
            $this->manager->persist($account);
            $this->manager->flush();
        }
    }

    public function onDealCreatedAction(DealCreatedEvent $event) {
        $event->getAccount()->setDealCount($event->getAccount()->getDealCount() + 1);
        if($event->getAccount()->getDealCount() == 10) {
            $account = $this->accountRepository->find($event->getAccount()->getId());
            $badge = $this->badgeRepository->findDealBadge();
            $account->addBadge($badge);
            $this->manager->persist($account);
            $this->manager->flush();
        }
    }
}
