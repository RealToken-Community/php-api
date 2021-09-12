<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\DefiService;
use App\Traits\DataControllerTrait;
use App\Traits\HeadersControllerTrait;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1")
 */
class DefiController
{
    use HeadersControllerTrait;
    use DataControllerTrait;

    /** @var AuthenticatorService */
    private AuthenticatorService $authenticatorService;
    /** @var DefiService */
    private DefiService $defiService;

    public function __construct(AuthenticatorService $authenticatorService, DefiService $defiService)
    {
        $this->authenticatorService = $authenticatorService;
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

    /**
     * Generate token symbol.
     *
     * @OA\Response(
     *     response=200,
     *     description="Generate token symbol",
     * )
     * @OA\Tag(name="DeFi")
     * @Security(name="api_key")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("/generateTokenSymbol", name="token_symbol_generate", methods={"POST"})
     */
    public function generateTokenSymbol(Request $request): JsonResponse
    {
        $this->authenticatorService->checkAdminRights($this->getApiToken($request));

        return $this->defiService->generateTokenSymbol();
    }
}
