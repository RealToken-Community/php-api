<?php

namespace App\Repository;

use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TokenRepository
 * @package App\Repository
 */
class TokenRepository extends ServiceEntityRepository
{
    /**
     * TokenRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    /**
     * Drop all tokens.
     *
     */
    public function dropTokens(): void
    {
        $this->createQueryBuilder('t')
            ->delete(Token::class)
            ->getQuery()
            ->execute();
    }
}
