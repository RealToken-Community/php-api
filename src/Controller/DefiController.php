<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\DefiService;
use App\Traits\DataControllerTrait;
use App\Traits\HeadersControllerTrait;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/v1', name: 'home')]
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
     * @param Request $request
     *
     * @deprecated
     *
     * @return JsonResponse
     */
    #[OA\Response(
        response: 301,
        description: 'Get deprecated'
    )]
    #[OA\Tag(name: 'DeFi')]
    #[Route('/tokenListOld', name: 'amm_list_deprecated', methods: ['GET'])]
    public function getTokenListDeprecated(Request $request): JsonResponse
    {
        return $this->defiService->getTokenListForAMMDeprecated($this->getReferer($request));
    }

    /**
     * RealToken list for AMM.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Return list of RealToken for Automatic Market Maker'
    )]
    #[OA\Tag(name: 'DeFi')]
    #[Route('/tokenList', name: 'amm_list', methods: ['GET'])]
    public function getTokenList(Request $request): JsonResponse
    {
        return $this->defiService->getTokenListForAMM($this->getReferer($request));
    }

    /**
     * RealToken history list.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Return list of RealToken history'
    )]
    #[OA\Tag(name: 'DeFi')]
    #[Route('/tokenHistory', name: 'token_history', methods: ['GET'])]
    public function getTokenHistory(Request $request): JsonResponse
    {
        return $this->defiService->getTokenHistory($this->getReferer($request));
    }

    /**
     * Generate token symbol.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[OA\Response(
        response: 200,
        description: 'Generate token symbol'
    )]
    #[OA\Tag(name: 'DeFi')]
    #[Security(name: 'api_key')]
    #[Route('/generateSymbol', name: 'token_symbol_generate', methods: ['POST'])]
    public function generateTokenSymbol(Request $request): JsonResponse
    {
        $this->authenticatorService->checkHydratorRights($this->getApiToken($request));

        return $this->defiService->generateTokenSymbol();
    }

    /**
     * Generate LP pair token.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception|InvalidArgumentException
     */
    #[OA\Response(
        response: 200,
        description: 'Generate LP pair token'
    )]
    #[OA\Tag(name: 'DeFi')]
    #[Security(name: 'api_key')]
//    #[IsGranted('ROLE_ADMIN')]
    #[Route('/generateLpPair', name: 'token_lp_pair_generate', methods: ['POST'])]
    public function generateLpPair(Request $request): JsonResponse
    {
        $this->authenticatorService->checkHydratorRights($this->getApiToken($request));

        return $this->defiService->generateLpPairToken();
    }
}
