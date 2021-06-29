<?php

namespace App\Controller;

use App\Service\DefiService;
use App\Traits\DataControllerTrait;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1")
 */
class DefiController
{
    use DataControllerTrait;

    /** @var DefiService */
    private DefiService $defiService;

    public function __construct(DefiService $defiService)
    {
        $this->defiService = $defiService;
    }

    /**
     * RealToken list for AMM.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of RealToken for Automatic Market Maker",
     * )
     * @OA\Tag(name="DeFi")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/tokenList", name="amm_list", methods={"GET"})
     */
    public function getTokenList(Request $request): JsonResponse
    {
        return $this->defiService->getTokenListForAMM($this->getRefer($request));
    }

    /**
     * RealToken list for AMM (beta).
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of RealToken for Automatic Market Maker (beta)",
     * )
     * @OA\Tag(name="DeFi")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/tokenListBeta", name="amm_list_beta", methods={"GET"})
     */
    public function getTokenListBeta(Request $request): JsonResponse
    {
        return $this->defiService->getTokenListForAMMBeta($this->getRefer($request));
    }
}
