<?php

namespace App\Repository;

use App\Entity\QuotaConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuotaConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuotaConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuotaConfiguration[]    findAll()
 * @method QuotaConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotaConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuotaConfiguration::class);
    }

    // /**
    //  * @return QuotaConfiguration[] Returns an array of QuotaConfiguration objects
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
    public function findOneBySomeField($value): ?QuotaConfiguration
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
