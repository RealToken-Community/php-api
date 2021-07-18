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
     * RealToken list for AMM (deprecated).
     *
     * @OA\Response(
     *     response=301,
     *     description="Get deprecated",
     * )
     * @OA\Tag(name="DeFi")
     *
     * @param Request $request
     *
     * @deprecated
     *
     * @return JsonResponse
     * @Route("/tokenListOld", name="amm_list_deprecated", methods={"GET"})
     */
    public function getTokenListDeprecated(Request $request): JsonResponse
    {
        return $this->defiService->getTokenListForAMMDeprecated($this->getRefer($request));
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
}
