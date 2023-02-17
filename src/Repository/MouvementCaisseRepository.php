<?php

namespace App\Repository;

use App\Entity\MouvementCaisse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MouvementCaisse|null find($id, $lockMode = null, $lockVersion = null)
 * @method MouvementCaisse|null findOneBy(array $criteria, array $orderBy = null)
 * @method MouvementCaisse[]    findAll()
 * @method MouvementCaisse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvementCaisseRepository extends ServiceEntityRepository
{
    private $yearRepository;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementCaisse::class);

    }

     /**
      * @return MouvementCaisse[] Returns an array of MouvementCaisse objects
      */

    public function findByCaisse($value)
    {
            return $this->createQueryBuilder('t')
                ->andWhere('t.caisseinit = :val')
                ->andWhere('t.yearschool = :year')
                ->setParameter('val', $value)
                ->orderBy('t.id', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * @param $datebegin
     * @param $dateend
     * @param $caisse
     * @return MouvementCaisse[] Returns an array of SaleArticle objects
     */
    public function findByPeriodeAndCaisse($datebegin,$dateend,$caisse): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb=$qb->select('s')
            ->andWhere("s.caisse = :shop")
            ->setParameter('shop',$caisse);
        $qb->andWhere('s.dateoperation >= :begin')
            ->andWhere('s.dateoperation <= :end')
            ->setParameter('begin',$datebegin )
            ->setParameter('end', $dateend.' 23:59')
            ->orderBy('s.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
    /**
     * @param $datebegin
     * @param $dateend
     * @param $shop
     * @return MouvementCaisse[] Returns an array of SaleArticle objects
     */
    public function findByPeriodeshop($datebegin,$dateend,$shop): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb=$qb->select('s')
            ->leftJoin('s.caisse','caisse')
            ->andWhere("caisse.shop = :shop")
            ->setParameter('shop',$shop);
        $qb->andWhere('s.dateoperation >= :begin')
            ->andWhere('s.dateoperation <= :end')
            ->setParameter('begin',$datebegin )
            ->setParameter('end', $dateend.' 23:59')
            ->orderBy('s.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
