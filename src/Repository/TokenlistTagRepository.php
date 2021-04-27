<?php

namespace App\Repository;

use App\Entity\TokenlistTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenlistTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenlistTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenlistTag[]    findAll()
 * @method TokenlistTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenlistTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenlistTag::class);
    }

    // /**
    //  * @return TokenlistTag[] Returns an array of TokenlistTag objects
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
    public function findOneBySomeField($value): ?TokenlistTag
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
