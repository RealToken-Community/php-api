<?php

namespace App\Repository;

use App\Entity\TokenMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenMapping|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenMapping|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenMapping[]    findAll()
 * @method TokenMapping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenMapping::class);
    }
}
