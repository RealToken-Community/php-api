<?php

namespace App\Repository;

use App\Entity\Quota;
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
}
