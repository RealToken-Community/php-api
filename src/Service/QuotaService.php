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
        $quotaService = $this->entityManager->getRepository(Quota::class);

        $quota = $quotaService->findOneBy(['application' => $application]);
        if (!$quota) {
            $quota = new Quota();
            $quota->setApplication($application);
        }
        $quota->setIncrement();
        $this->entityManager->persist($quota);
        $this->entityManager->flush();

        if ($application->getQuota() === null) {
            $application->setQuota($quota);
            $this->entityManager->persist($application);
            $this->entityManager->flush();
        }
    }
}