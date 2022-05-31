<?php

namespace App\Repository;

use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TokenRepository
 * @package App\Repository
 *
 * @method Token[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
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
     * Count total of tokens.
     *
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countAllTokens()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
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

    /**
     * Show last token updated.
     *
     * @return array
     */
    public function getLastTokenUpdated(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Show last token update time.
     *
     * @return string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getLastTokenUpdateTime()
    {
        return $this->createQueryBuilder('t')
            ->select('t.lastUpdate')
            ->orderBy('t.lastUpdate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
