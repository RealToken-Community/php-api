<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\QuotaService;
use App\Traits\DataControllerTrait;
use App\Traits\HeadersControllerTrait;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/v1/quota")]
class QuotaController
{
    use HeadersControllerTrait;
    use DataControllerTrait;

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
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Return list of user quotas',
    )]
    #[OA\Tag(name: 'Quotas')]
    #[Security(name: 'api_key')]
    #[Route("", name: 'user_quota', methods: ['GET'])]
    public function showQuotas(Request $request): JsonResponse
    {
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        return $this->quotaService->getUserQuotas($apiKey);
    }
}
