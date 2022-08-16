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
    /**
     * Get user quotas.
     *
     * @param string|null $apiKey
     *
     * @return JsonResponse
     */
    public function getUserQuotas(?string $apiKey): JsonResponse
    {
        if (empty($apiKey)) {
            return new JsonResponse(
                ["status" => "error", "message" => "Api key not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $applicationRepository = $this->em->getRepository(Application::class);
        $application = $applicationRepository->findOneBy(['apiToken' => $apiKey]);

        $roles = $application->getUser()->getRoles();

        if (($key = array_search("ROLE_USER", $roles)) !== false) {
            unset($roles[$key]);
        }

        if (empty(array_values($roles))) {
            return new JsonResponse(
                ["status" => "error", "message" => "Not admin user"],
                Response::HTTP_FORBIDDEN
            );
        }

        $role = array_values($roles)[0];

        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
        $quotaLimitation = $quotaLimitationsRepository->findOneBy(['role' => $role]);

        return new JsonResponse($quotaLimitation->__toArray(),Response::HTTP_OK);
    }
}
