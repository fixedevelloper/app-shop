<?php

namespace App\Repository;

use App\Entity\SaleArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\s;

/**
 * @extends ServiceEntityRepository<SaleArticle>
 *
 * @method SaleArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleArticle[]    findAll()
 * @method SaleArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleArticle::class);
    }

    public function save(SaleArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SaleArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $datebegin
     * @param $dateend
     * @param $shop
     * @return SaleArticle[] Returns an array of SaleArticle objects
     */
    public function findByPeriode($datebegin,$dateend,$shop): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb=$qb->select('s')
            ->leftJoin('s.sellerShop','sellerShop')
            ->andWhere("sellerShop.shop = :shop")
            ->setParameter('shop',$shop);
        $qb->andWhere('s.date_created >= :begin')
            ->andWhere('s.date_created <= :end')
            ->setParameter('begin',$datebegin )
            ->setParameter('end', $dateend.' 23:59')
            ->orderBy('s.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $datebegin
     * @param $dateend
     * @param $seller
     * @return SaleArticle[] Returns an array of SaleArticle objects
     */
    public function findByPeriodeAndSeller($datebegin,$dateend,$seller): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb=$qb->select('s')
            ->andWhere("s.sellerShop = :seller")
            ->setParameter('seller',$seller);
        $qb->andWhere('s.date_created >= :begin')
            ->andWhere('s.date_created <= :end')
            ->setParameter('begin',$datebegin )
            ->setParameter('end', $dateend.' 23:59')
            ->orderBy('s.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?SaleArticle
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
