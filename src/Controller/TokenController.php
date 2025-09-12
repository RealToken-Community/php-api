<?php

namespace App\Controller;

use App\Entity\Token;
use App\Service\RequestContextService;
use App\Service\TokenService;
use App\Traits\DataControllerTrait;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Psr\Cache\CacheItemPoolInterface;

#[Route("/v1/token")]
class TokenController
{
  use DataControllerTrait;

  private TokenService $tokenService;

  public function __construct(TokenService $tokenService)
  {
    $this->tokenService = $tokenService;
  }

  /**
   * Show latest token updated.
   *
   * @param RequestContextService $ctx
   * @return JsonResponse
   */
  #[OA\Response(
    response: 200,
    description: 'Return last token updated',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Route("/lastUpdate", name: 'token_last_updated', methods: ['GET'])]
  public function showLatestUpdated(RequestContextService $ctx): JsonResponse
  {
    return $this->tokenService->showLatestUpdated($ctx);
  }

  /**
   * Show latest token update time.
   *
   * @return JsonResponse
   * @throws Exception
   */
  #[OA\Response(
    response: 200,
    description: 'Return last update time',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Route("/lastUpdateTime", name: 'token_last_update_time', methods: ['GET'])]
  public function showLatestUpdateTime(): JsonResponse
  {
    return $this->tokenService->showLatestUpdateTime();
  }

  /**
   * List all tokens (deprecated).
   *
   * @param RequestContextService $ctx
   * @return JsonResponse
   * @deprecated
   *
   */
  #[OA\Response(
    response: 301,
    description: 'Get deprecated',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("s", name: 'tokens_show_deprecated', methods: ['GET'])]
  public function showTokensDeprecated(RequestContextService $ctx): JsonResponse
  {
    return $this->tokenService->getTokens($ctx, true);
  }

	/**
	 * List all tokens.
	 *
	 * @param RequestContextService $ctx
	 * @return JsonResponse
	 * @throws InvalidArgumentException
	 */
  #[OA\Response(
    response: 200,
    description: 'Return list of tokens',
//        content: new Model(type: Token::class)
//        content: new Model(type: Quota::class)
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(ref: new Model(type: Token::class, groups: ['full']))
    )
//        content: new OA\JsonContent(
//            type: 'array',
//            items: new OA\Items(ref: new Model(type: Token::class))
//        )
//        content: new OA\JsonContent(example: new OA\Schema(
//            type: 'object',
//            properties: [
//                new OA\Property(property: 'uuid', ref: new Model(type: Token::class))
//            ]
//        ))
  )]
  #[OA\Response(
    response: 429,
    description: 'Too many requests',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("", name: 'tokens_show', methods: ['GET'])]
  public function showTokens(RequestContextService $ctx, CacheItemPoolInterface $cache): JsonResponse
//  public function showTokens(RequestContextService $ctx): JsonResponse
  {
		// Check user authentication and roles
//		$userAuth = ;
		$userAuth = [
			'isAuthenticated' => $ctx->isAuthenticated(),
			'isAdmin' => $ctx->isAdmin()
		];

		$cacheItem = $cache->getItem('tokens_cache');

		if (!$cacheItem->isHit()) {
			print_r($cacheItem);
			exit();
			$tokens = $this->tokenService->getTokens($userAuth);
			$cacheItem->set($tokens);
			$cache->save($cacheItem);
		} else {
			$tokens = $cacheItem->get();
		}

	    return $tokens;
  }

  /**
   * Return data from token.
   *
   * @param RequestContextService $ctx
   * @param string $uuid
   *
   * @return JsonResponse
   */
  #[OA\Response(
    response: 200,
    description: 'Return data from token',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("/{uuid}", name: 'token_show', methods: ['GET'])]
  public function showToken(RequestContextService $ctx, string $uuid) : JsonResponse
  {
    return $this->tokenService->getToken($ctx, $uuid);
  }

  /**
   * Update token data.
   *
   * @param Request $request
   * @param string $uuid
   *
   * @return JsonResponse
   * @throws Exception
   */
  #[OA\Response(
    response: 200,
    description: 'Update token data',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("/{uuid}", name: 'token_update', methods: ['PUT'])]
  public function updateToken(Request $request, string $uuid) : JsonResponse
  {
    return $this->tokenService->updateToken($uuid, $this->getDataJson($request));
  }

  /**
   * Delete token.
   *
   * @param Request $request
   * @param string $uuid
   *
   * @return JsonResponse
   */
  #[OA\Response(
    response: 200,
    description: 'Delete token',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("/{uuid}", name: 'token_delete', methods: ['DELETE'])]
  public function deleteToken(Request $request, string $uuid): JsonResponse
  {
    return $this->tokenService->deleteToken($uuid);
  }

  /**
   * Create token data (deprecated).
   *
   * @param Request $request
   *
   * @return JsonResponse
   * @throws Exception|\Doctrine\DBAL\Exception
   * @deprecated
   */
  #[OA\Response(
    response: 301,
    description: 'Create deprecated',
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("s", name: 'token_create_deprecated', methods: ['POST'])]
  public function createTokenDeprecated(Request $request): JsonResponse
  {
    return $this->tokenService->createToken($this->getDataJson($request), true);
  }

  /**
   * Create token data.
   *
   * @param Request $request
   *
   * @return JsonResponse
   * @throws Exception|\Doctrine\DBAL\Exception
   */
  #[OA\Response(
    response: 200,
    description: 'Create token data'
  )]
  #[OA\Tag(name: 'Tokens')]
  #[Security(name: 'api_key')]
  #[Route("", name: 'token_create', methods: ['POST'])]
  public function createToken(Request $request): JsonResponse
  {
    return $this->tokenService->createToken($this->getDataJson($request));
  }
}
