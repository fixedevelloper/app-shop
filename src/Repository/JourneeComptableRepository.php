<?php

namespace App\Repository;

use App\Entity\JourneeComptable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JourneeComptable|null find($id, $lockMode = null, $lockVersion = null)
 * @method JourneeComptable|null findOneBy(array $criteria, array $orderBy = null)
 * @method JourneeComptable[]    findAll()
 * @method JourneeComptable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourneeComptableRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JourneeComptable::class);
    }
    public function findOneBycaisseactuve($value): ?JourneeComptable
    {
        $date=new \DateTime('now');
        $date->sub(new \DateInterval('P1D'));
        return $this->createQueryBuilder('c')
            ->andWhere('c.caisse = :val')
            ->andWhere('c.status = 1')
            ->andWhere('c.datecomptable <= :date')
            ->setParameter('date',$date)
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


}
