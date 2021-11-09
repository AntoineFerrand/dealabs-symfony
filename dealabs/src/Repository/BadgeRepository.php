<?php

namespace App\Repository;

use App\Entity\Badge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Badge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badge[]    findAll()
 * @method Badge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }

    public function findVoteBadge()
    {
        return $this->findBadge("Surveillant");
    }

    public function findCommentBadge()
    {
        return $this->findBadge("Cobaye");
    }

    public function findDealBadge()
    {
        return $this->findBadge("Rapport de stage");
    }

    public function findBadge($query)
    {
        try {
            return $this->createQueryBuilder('b')
                ->andWhere('b.name = :name')
                ->setParameter('name', $query)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }
    }

    public function findBadgeByUser($user)
    {
        return $this->createQueryBuilder('b')
            ->join('b.accounts', 'a')
            ->where('a.id = :id')
            ->setParameter('id', $user->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }
}
