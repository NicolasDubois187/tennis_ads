<?php

namespace App\Repository;

use App\Entity\Ads;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ads|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ads|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ads[]    findAll()
 * @method Ads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ads::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Ads $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Ads $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function getAdsByTypes($title, $adType, $brand, $materialType)
    {
        $queryBuilder = $this->createQueryBuilder('ads')

            ->select('ads')
            ->leftJoin('ads.adType', 'adType')
            ->addSelect('adType')
            ->leftJoin('ads.brand', 'brand')
            ->addSelect('brand')
            ->leftJoin('ads.materialType', 'materialType')
            ->addSelect('materialType')
            ->leftJoin('ads.media', 'media')
            ->addSelect('media')
            ;
            if ($title) {
                $queryBuilder
                    ->where('ads.title LIKE :title')
                    ->setParameter('title', '%' . $title . '%');
            }
            if ($adType) {
                $queryBuilder
                    ->andWhere('adType.name LIKE :adType')
                    ->setParameter('adType', '%' . $adType . '%');
            }
            if ($brand) {
                $queryBuilder
                    ->andWhere('brand.name LIKE :brand')
                    ->setParameter('brand', '%' . $brand . '%');
            }
            if ($materialType) {
                $queryBuilder
                    ->andWhere('materialType.name LIKE :materialType')
                    ->setParameter('materialType', '%' . $materialType . '%');
            }
            $query = $queryBuilder
                ->orderBy('ads.date', 'DESC')
                ->getQuery()
        ;
        $ads = $query->getArrayResult();
        return $ads;
    }

    // /**
    //  * @return Ads[] Returns an array of Ads objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ads
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
