<?php

namespace App\Repository;

use App\Entity\Personnel;
use App\Entity\SessionCaisse;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionCaisse|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionCaisse|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionCaisse[]    findAll()
 * @method SessionCaisse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionCaisseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionCaisse::class);
    }
    public function findOneByLast(Personnel $personnel)
    {
        return $this->createQueryBuilder('s')
            ->where('s.personnel = :val')
            ->andWhere('s.active =1')
            ->setParameter('val', $personnel)
            ->setMaxResults(1)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    // /**
    //  * @return SessionCaisse[] Returns an array of SessionCaisse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SessionCaisse
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
