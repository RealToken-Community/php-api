<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * Get fully detailed quota.
     *
     * @return array
     */
    public function findAllWithQuota(): array
    {
        return $this->_em
            ->getRepository(Application::class)
            ->createQueryBuilder('a')
            ->leftJoin('a.quota', 'q')
            ->leftJoin('a.user', 'u')
            ->getQuery()
            ->getResult();
    }
}
