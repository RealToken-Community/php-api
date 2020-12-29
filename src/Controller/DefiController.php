<?php

namespace App\Controller;

use App\Service\DefiService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1")
 */
class DefiController
{
    /**
     * RealToken list for AMM.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of RealToken for Automatic Market Maker",
     * )
     * @OA\Tag(name="DeFi")
     * @param DefiService $defiService
     *
     * @return JsonResponse
     * @Route("/tokenList", name="amm_list", methods={"GET"})
     */
    public function getTokenList(DefiService $defiService): JsonResponse
    {
        return $defiService->getTokenListForAMM();
    }
}