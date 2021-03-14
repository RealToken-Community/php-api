<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\Quota;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quota|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quota|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quota[]    findAll()
 * @method Quota[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quota::class);
    }

    /**
     * Get fully detailed quota.
     *
     * @return array
     */
    public function findAllDetailedQuota(): array
    {
        return $this->_em
            ->getRepository(Quota::class)
            ->createQueryBuilder('q')
            ->join('q.application', 'a')
            ->join('a.user', 'u')
            ->getQuery()
            ->getResult();
    }
}
