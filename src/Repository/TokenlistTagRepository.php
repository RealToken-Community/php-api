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

    /**
     * Get tags from ids.
     *
     * @param array $ids
     * @return array
     */
    public function findAllWithIds(array $ids): array
    {
        return $this->_em
            ->getRepository(TokenlistTag::class)
            ->createQueryBuilder('t')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get findAll() in array.
     *
     * @return array
     */
    public function findAllArrayResponse(): array
    {
        $query = $this->_em
            ->getRepository(TokenlistTag::class)
            ->createQueryBuilder('t')
            ->getQuery();
        return $query->getArrayResult();
    }
}
