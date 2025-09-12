<?php

namespace App\Controller;

use App\Service\DefiService;
use App\Service\RequestContextService;
use App\Traits\DataControllerTrait;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/v1')]
class DefiController
{
  use DataControllerTrait;

  private DefiService $defiService;

  public function __construct(DefiService $defiService)
  {
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
   * @return JsonResponse
   */
  #[OA\Response(
    response: 200,
    description: 'Return list of RealToken history'
  )]
  #[OA\Tag(name: 'DeFi')]
  #[Route('/tokenHistory', name: 'token_history', methods: ['GET'])]
  public function getTokenHistory(): JsonResponse
  {
    return $this->defiService->getTokenHistory();
  }

  /**
   * Generate token symbol.
   *
   * @param RequestContextService $ctx
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
  public function generateTokenSymbol(RequestContextService $ctx): JsonResponse
  {
    if (!$ctx->isHydrator()) {
      return new JsonResponse(
        ['error' => 'Unauthorized'],
        Response::HTTP_UNAUTHORIZED
      );
    } else {
      return $this->defiService->generateTokenSymbol();
    }
  }

  /**
   * Generate LP pair token.
   *
   * @param RequestContextService $ctx
   * @return JsonResponse
   * @throws InvalidArgumentException
   */
  #[OA\Response(
    response: 200,
    description: 'Generate LP pair token'
  )]
  #[OA\Tag(name: 'DeFi')]
  #[Security(name: 'api_key')]
  #[Route('/generateLpPair', name: 'token_lp_pair_generate', methods: ['POST'])]
  public function generateLpPair(RequestContextService $ctx): JsonResponse
  {
    if (!$ctx->isHydrator()) {
      return new JsonResponse(
        ['error' => 'Unauthorized'],
        Response::HTTP_UNAUTHORIZED
      );
    } else {
      return $this->defiService->generateLpPairToken();
    }
  }
}
