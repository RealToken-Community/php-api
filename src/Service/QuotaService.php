<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\QuotaLimitations;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class QuotaService
 * @package App\Service
 */
class QuotaService extends Service
{
    public function getUserQuotas(string $apiKey) {
        $applicationRepository = $this->em->getRepository(Application::class);
        $application = $applicationRepository->findOneBy(['apiToken' => $apiKey]);

        $roles = $application->getUser()->getRoles();

        if (($key = array_search("ROLE_USER", $roles)) !== false) {
            unset($roles[$key]);
        }

        if (empty(array_values($roles))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, "Not admin user");
        }

        $role = array_values($roles)[0];

        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
        $quotaLimitation = $quotaLimitationsRepository->findOneBy(['role' => $role]);

        return new JsonResponse($quotaLimitation->__toArray(),Response::HTTP_OK);
    }
}
