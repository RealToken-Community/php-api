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

    public function findAllDetailledQuota()
    {
        $rqt = $this->_em->createQueryBuilder();
        $rqt->select('u, a, q')
            ->from(Quota::class, 'q')
            ->leftJoin(Application::class, 'a', 'WITH','a.id = q.id')
            ->leftJoin(User::class, 'u', 'WITH','u.id = a.id');

        return $rqt->getQuery()->getResult();
    }
}
