<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\QuotaService;
use App\Service\TokenService;
use App\Traits\HeadersControllerTrait;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/quota")
 */
class QuotaController
{
    use HeadersControllerTrait;

    /** @var AuthenticatorService */
    private AuthenticatorService $authenticatorService;
    /** @var QuotaService */
    private QuotaService $quotaService;

    public function __construct(AuthenticatorService $authenticatorService, QuotaService $quotaService)
    {
        $this->authenticatorService = $authenticatorService;
        $this->quotaService = $quotaService;
    }

    /**
     * Get user quotas.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of user quotas",
     * )
     * @OA\Tag(name="Quotas")
     * @Security(name="api_key")
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/", name="user_quota", methods={"GET"})
     */
    public function showTokens(Request $request): JsonResponse
    {
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights($apiKey);

        return $this->quotaService->getUserQuotas($apiKey);
    }
}
