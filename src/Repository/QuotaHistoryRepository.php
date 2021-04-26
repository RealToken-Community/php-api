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

    /**
     * Find last usage from quota id.
     *
     * @param Quota $quota
     *
     * @return array
     * @throws \Exception
     */
    public function findLastUsage2(Quota $quota): array
    {
        $minute = new DateTime("1 minute ago");
        $hour = new DateTime("1 hour ago");
        $day = new DateTime("1 day ago");
        $week = new DateTime("1 week ago");
        $month = new DateTime("1 month ago");
        $year = new DateTime("1 year ago");

        return [
            'year' => $this->getTimeUsage($quota, $year),
            'month' => $this->getTimeUsage($quota, $month),
            'week' => $this->getTimeUsage($quota, $week),
            'day' => $this->getTimeUsage($quota, $day),
            'hour' => $this->getTimeUsage($quota, $hour),
            'minute' => $this->getTimeUsage($quota, $minute)
        ];
//
//        $sql = '
//        SELECT (SELECT COUNT(*)
//            FROM `quota_history`
//            WHERE `quota_id` = :quota
//            AND `access_time` > :minute
//        ) AS `minute`,
//        (SELECT COUNT(*)
//            FROM `quota_history`
//            WHERE `quota_id` = :quota
//            AND `access_time` > :hour
//        ) AS `hour`,
//        (SELECT COUNT(*)
//            FROM `quota_history`
//            WHERE `quota_id` = :quota
//            AND `access_time` > :day
//        ) AS `day`,
//        (SELECT COUNT(*)
//            FROM `quota_history`
//            WHERE `quota_id` = :quota
//            AND `access_time` > :week
//        ) AS `week`,
//        (SELECT COUNT(*)
//            FROM `quota_history`
//            WHERE `quota_id` = :quota
//            AND `access_time` > :month
//        ) AS `month`,
//        (SELECT COUNT(*)
//            FROM `quota_history`
//            WHERE `quota_id` = :quota
//            AND `access_time` > :year
//        ) AS `year`
//        FROM `quota_history`
//        LIMIT 1';
    }

    /**
     * Get time usage.
     *
     * @param Quota $quota
     * @param DateTime $accessTime
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function getTimeUsage(Quota $quota, DateTime $accessTime): int
    {
        return (int)$this->createQueryBuilder('qh')
            ->select('count(qh.id)')
            ->where('qh.quota = :quota')
            ->andWhere('qh.accessTime > :accessTime')
            ->setParameter('quota', $quota->getId())
            ->setParameter('accessTime', $accessTime->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
