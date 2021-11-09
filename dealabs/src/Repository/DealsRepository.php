<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Deals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;

/**
 * @method Deals|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deals|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deals[]    findAll()
 * @method Deals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deals::class);
    }

    public function calculateTemperatureDeal($deals){
        if($deals != null) {
            foreach($deals as $deal) {
                $votes = $deal->getVotes();
                foreach($votes as $vote) {
                    if($vote->getIsPositive()) {
                        $deal->upVote();
                    } else {
                        $deal->downVote();
                    }
                }
            }
            return $deals;
        }
        return null;

    }

    public function findAllGreaterthan100(): array
    {
        $deals = $this->calculateTemperatureDeal($this->findAll());
        $deals = array_filter($deals, function($deal) {
            return $deal->getVote() >= 100;
        });
        usort($deals, function ($a, $b){
            return $a->getVote() < $b->getVote();
        });

        return $deals;
    }

    public function findSortedBonsPlans()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->setParameter('type', "deal")
            ->orderBy('d.creationDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findSortedCodesPromos()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type = :type')
            ->setParameter('type', "code")
            ->orderBy('d.creationDate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByKeyWord($keyword): array
    {
        $deals = $this->createQueryBuilder('d')
            ->join('d.partenaire', 'p')
            ->join('d.groupes', 'g')
            ->where('d.title like :title')
            ->setParameter('title', "%".$keyword."%")
            ->orWhere('d.description like :description')
            ->setParameter('description', "%".$keyword."%")
            ->orWhere('p.nom like :nom')
            ->setParameter('nom', "%".$keyword."%")
            ->orWhere('g.nom like :nom')
            ->setParameter('nom', "%".$keyword."%")
            ->getQuery()
            ->getResult()
        ;
        $deals = $this->calculateTemperatureDeal($deals);
        if($deals == null) {
            return array();
        } else {
            return $deals;
        }
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneByKeyWord($keyword): ?Deals
    {
        $deal = $this->createQueryBuilder('d')
            ->join('d.groupes', 'g')
            ->join('d.partenaire', 'p')
            ->where('d.title like :title')
            ->setParameter('title', "%".$keyword."%")
            ->orWhere('d.description like :description')
            ->setParameter('description', "%".$keyword."%")
            ->orWhere('p.nom like :nom')
            ->setParameter('nom', "%".$keyword."%")
            ->orWhere('g.nom like :nom')
            ->setParameter('nom', "%".$keyword."%")
            ->orderBy('d.creationDate','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
        return $deal;
    }

    public function getHottestDeal($deals): int{
        $hottestDeal = 0;
        if($deals !== null){
            foreach ($deals as $deal){
                if ($deal->getVote() > $hottestDeal){
                    $hottestDeal = $deal->getVote();
                }
            }
        }
        return $hottestDeal;
    }

    public function getMoyenneDeal($deals, $user): int{
        $moyenne = 0;
        $count = 0;
        if($deals !== null){
            foreach ($deals as $deal){
                if ($deal->getCreator() == $user)
                    $moyenne += $deal->getVote();
                    $count += 1;
            }
            if ($count !== 0)
                $moyenne /= $count;
        }
        return $moyenne;
    }

    public function getLastYear(): array
    {
        $deals = $this->createQueryBuilder('d')
            ->where('d.creationDate > :time')
            ->setParameter('time', (time()-31104000))
            ->getQuery()
            ->getResult();
        return $deals;
    }

    public function getHotPercent($deals): int{
        $oui = 0;
        $non = 0;
        $pourcentage = 0;
        $count = 0;
        if($deals !== null){
            foreach ($deals as $deal){
                if ($deal->getVote() >= 100)
                    $oui += 1;
                $count += 1;
            }
            $non = $count - $oui;
            if ($count !== 0)
                $pourcentage = ($oui/$non)*100;
        }
        return $pourcentage;
    }

    public function getDealsSortByDate($deals): array
    {
        usort($deals, function ($a, $b){
            return $a->getCreationDate() < $b->getCreationDate();
        });

        return $deals;
    }
}