<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\TokenService;
use App\Traits\DataControllerTrait;
use App\Traits\HeadersControllerTrait;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/token")
 */
class TokenController
{
    use HeadersControllerTrait;
    use DataControllerTrait;

    /** @var AuthenticatorService */
    private AuthenticatorService $authenticatorService;
    /** @var TokenService */
    private TokenService $tokenService;

    public function __construct(AuthenticatorService $authenticatorService, TokenService $tokenService)
    {
        $this->authenticatorService = $authenticatorService;
        $this->tokenService = $tokenService;
    }

    /**
     * Show latest token updated.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/lastUpdate", name="token_last_updated", methods={"GET"})
     */
    public function showLatestUpdated(Request $request): JsonResponse
    {
        $credentials = $this->authenticatorService->checkCredentials(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->showLatestUpdated($credentials);
    }

    /**
     * Show latest token update time.
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("/lastUpdateTime", name="token_last_update_time", methods={"GET"})
     */
    public function showLatestUpdateTime(): JsonResponse
    {
        return $this->tokenService->showLatestUpdateTime();
    }

    /**
     * List all tokens (deprecated).
     *
     * @OA\Response(
     *     response=301,
     *     description="Get deprecated",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     *
     * @deprecated
     *
     * @return JsonResponse
     * @Route("s", name="tokens_show_deprecated", methods={"GET"})
     */
    public function showTokensDeprecated(Request $request): JsonResponse
    {
        $credentials = $this->authenticatorService->checkCredentials(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->getTokens($credentials, true);
    }

    /**
     * List all tokens.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of tokens",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="tokens_show", methods={"GET"})
     */
    public function showTokens(Request $request): JsonResponse
    {
        $credentials = $this->authenticatorService->checkCredentials(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->getTokens($credentials);
    }
    
    /**
     * Return data from token.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return data from token",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     * @param string $uuid
     *
     * @return JsonResponse
     * @Route("/{uuid}", name="token_show", methods={"GET"})
     */
    public function showToken(Request $request, string $uuid) : JsonResponse
    {
        $credentials = $this->authenticatorService->checkCredentials(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->getToken($credentials, $uuid);
    }

    /**
     * Update token data.
     *
     * @OA\Response(
     *     response=200,
     *     description="Update token data",
     * )
     * @OA\RequestBody(
     *     request="token",
     *     description="JSON data token",
     *     @OA\JsonContent(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     * @param string $uuid
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("/{uuid}", name="token_update", methods={"PUT"})
     */
    public function updateToken(Request $request, string $uuid) : JsonResponse
    {
        $this->authenticatorService->checkAdminRights(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->updateToken($uuid, $this->getDataJson($request));
    }

    /**
     * Delete token.
     *
     * @OA\Response(
     *     response=200,
     *     description="Delete token",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     * @param string $uuid
     *
     * @return JsonResponse
     * @Route("/{uuid}", name="token_delete", methods={"DELETE"})
     */
    public function deleteToken(Request $request, string $uuid): JsonResponse
    {
        $this->authenticatorService->checkAdminRights(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->deleteToken($uuid);
    }

    /**
     * Create token data (deprecated).
     *
     * @OA\Response(
     *     response=301,
     *     description="Create deprecated",
     * )
     * @OA\RequestBody(
     *     request="token",
     *     description="JSON data token",
     *     @OA\JsonContent(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     *
     * @deprecated
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("s", name="token_create_deprecated", methods={"POST"})
     */
    public function createTokenDeprecated(Request $request): JsonResponse
    {
        $this->authenticatorService->checkAdminRights(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->createToken($this->getDataJson($request), true);
    }

    /**
     * Create token data.
     *
     * @OA\Response(
     *     response=200,
     *     description="Create token data",
     * )
     * @OA\RequestBody(
     *     request="token",
     *     description="JSON data token",
     *     @OA\JsonContent(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("", name="token_create", methods={"POST"})
     */
    public function createToken(Request $request): JsonResponse
    {
        $this->authenticatorService->checkAdminRights(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return $this->tokenService->createToken($this->getDataJson($request));
    }
}

