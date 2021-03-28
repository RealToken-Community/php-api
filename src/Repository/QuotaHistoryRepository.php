<?php

namespace App\Repository;

use App\Entity\Quota;
use App\Entity\QuotaHistory;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method QuotaHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuotaHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuotaHistory[]    findAll()
 * @method QuotaHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotaHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuotaHistory::class);
    }

    /**
     * Find last usage from quota id.
     *
     * @param Quota $quota
     * @param DateTime|null $datetime
     *
     * @return int
     * @throws \Exception
     */
    public function findLastUsage(Quota $quota, DateTime $datetime = null): int
    {
        try {
            $query = $this->createQueryBuilder('qh')
                ->select('COUNT(qh.id)')
                ->where('qh.quota = :quota')
                ->andWhere('qh.accessTime > :accessTime')
                ->setParameter('quota', $quota)
                ->setParameter('accessTime', $datetime)
                ->orderBy('qh.id', 'DESC')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new \Exception($e);
        }

        return (int)$query;
    }
}
