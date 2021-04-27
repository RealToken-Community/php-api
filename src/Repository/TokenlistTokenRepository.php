<?php

namespace App\Repository;

use App\Entity\TokenlistToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenlistToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenlistToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenlistToken[]    findAll()
 * @method TokenlistToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenlistTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenlistToken::class);
    }

    // /**
    //  * @return TokenlistToken[] Returns an array of TokenlistToken objects
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
    public function findOneBySomeField($value): ?TokenlistToken
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
