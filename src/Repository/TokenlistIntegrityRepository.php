<?php

namespace App\Repository;

use App\Entity\TokenlistIntegrity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenlistIntegrity|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenlistIntegrity|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenlistIntegrity[]    findAll()
 * @method TokenlistIntegrity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenlistIntegrityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenlistIntegrity::class);
    }

    /**
     * Get types from ids.
     *
     * @param array $ids
     * @return array
     */
    public function findAllWithIds(array $ids): array
    {
        return $this->_em
            ->getRepository(TokenlistIntegrity::class)
            ->createQueryBuilder('t')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
