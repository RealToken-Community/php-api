<?php

namespace App\Service;

use App\Entity\Quota;
use App\Entity\User;
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

    public function consumeQuota(User $user)
    {
        $em = $this->entityManager;
        $quotaService = $em->getRepository(Quota::class);

        $quota = $quotaService->findOneBy(['user_id' => $user->getId()]);
        if (!$quota) {
            $quota = new Quota();
            $quota->setUserId($user);
        }
        $quota->setIncrement();
        $em->persist($quota);
        $em->flush();
    }
}