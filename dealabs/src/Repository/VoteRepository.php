<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function findVotesByDeals($deals)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.deals = :deal')
            ->setParameter('deal', $deals)
            ->getQuery()
            ->getResult();
    }

    public function findByAccoutDeal($account, $deal)
    {
        try {
            return $this->createQueryBuilder('v')
                ->andWhere('v.account = :account')
                ->setParameter('account', $account)
                ->andWhere('v.deals = :deal')
                ->setParameter('deal', $deal)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function isVotable($deal, $account, $isPositive): int
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.account = :account')
            ->setParameter('account', $account)
            ->andWhere('v.deals = :deal')
            ->setParameter('deal', $deal)
            ->andWhere('v.isPositive = :isPositive')
            ->setParameter('isPositive', $isPositive);
        return count($qb->getQuery()->getResult());
    }

    public function canUpVote($deal, $account): bool
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.account = :account')
            ->setParameter('account', $account)
            ->andWhere('v.deals = :deal')
            ->setParameter('deal', $deal)
            ->andWhere('v.isPositive = :isPositive')
            ->setParameter('isPositive', 1);
        $cpt = count($qb->getQuery()->getResult());
        return $cpt == 0;
    }

    public function canDownVote($deal, $account): bool
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.account = :account')
            ->setParameter('account', $account)
            ->andWhere('v.deals = :deal')
            ->setParameter('deal', $deal)
            ->andWhere('v.isPositive = :isPositive')
            ->setParameter('isPositive', 0);
        $cpt = count($qb->getQuery()->getResult());
        return $cpt == 0;
    }


}