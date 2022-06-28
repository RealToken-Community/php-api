<?php

namespace App\Repository;

use App\Entity\QuotaLimitations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuotaLimitations|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuotaLimitations|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuotaLimitations[]    findAll()
 * @method QuotaLimitations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotaLimitationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuotaLimitations::class);
    }

    /**
     * Get array with limitations.
     *
     * @return array
     */
    public function getLimitationsValues(): array
    {
        return $this->_em
            ->getRepository(QuotaLimitations::class)
            ->createQueryBuilder('ql')
            ->getQuery()
            ->getResult();
    }
}
