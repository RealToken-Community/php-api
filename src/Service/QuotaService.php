<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Quota;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class QuotaService
 * @package App\Service
 */
class QuotaService
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Increment API quota.
     *
     * @param Application $application
     */
    public function consumeQuota(Application $application)
    {
        $em = $this->entityManager;
        $quotaService = $em->getRepository(Quota::class);

        $quota = $quotaService->findOneBy(['application' => $application]);
        if (!$quota) {
            $quota = new Quota();
            $quota->setApplication($application);
        }
        $quota->setIncrement();
        $em->persist($quota);
        $em->flush();
    }
}