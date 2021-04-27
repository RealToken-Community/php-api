<?php

namespace App\Repository;

use App\Entity\TokenlistNetwork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenlistNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenlistNetwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenlistNetwork[]    findAll()
 * @method TokenlistNetwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenlistNetworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenlistNetwork::class);
    }

    // /**
    //  * @return TokenlistNetwork[] Returns an array of TokenlistNetwork objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TokenlistNetwork
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
