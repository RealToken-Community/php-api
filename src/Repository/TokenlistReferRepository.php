<?php

namespace App\Repository;

use App\Entity\TokenlistRefer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenlistRefer|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenlistRefer|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenlistRefer[]    findAll()
 * @method TokenlistRefer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenlistReferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenlistRefer::class);
    }

    // /**
    //  * @return TokenlistRefer[] Returns an array of TokenlistRefer objects
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
    public function findOneBySomeField($value): ?TokenlistRefer
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
