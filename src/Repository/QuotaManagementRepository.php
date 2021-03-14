<?php

namespace App\Repository;

use App\Entity\QuotaManagement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuotaManagement|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuotaManagement|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuotaManagement[]    findAll()
 * @method QuotaManagement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotaManagementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuotaManagement::class);
    }

    // /**
    //  * @return QuotaManagement[] Returns an array of QuotaManagement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuotaManagement
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
