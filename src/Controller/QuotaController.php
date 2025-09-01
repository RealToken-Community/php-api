<?php

namespace App\Controller;

use App\Service\QuotaService;
use App\Service\RequestContextService;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/v1/quota")]
class QuotaController
{
    private QuotaService $quotaService;

    public function __construct(QuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    /**
     * Get user quotas.
     *
     * @param Request $request
     * @param RequestContextService $ctx
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Return list of user quotas',
    )]
    #[OA\Tag(name: 'Quotas')]
    #[Security(name: 'api_key')]
    #[Route("", name: 'user_quota', methods: ['GET'])]
    public function showQuotas(Request $request, RequestContextService $ctx): JsonResponse
    {
        return new JsonResponse([
            'quotas' => $this->quotaService->getUserQuotas($request, $ctx),
        ]);
    }
}
