<?php

namespace App\Controller;

use App\Service\TokenService;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1")
 */
class TokenController
{
    /**
     * List all tokens.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of tokens",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="api_key")
     * @param TokenService $tokenService
     *
     * @return JsonResponse
     * @Route("/tokens", name="tokens_show", methods={"GET"})
     */
    public function showTokens(TokenService $tokenService): JsonResponse
    {
        return $tokenService->getTokens();
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
     * @param TokenService $tokenService
     * @param string $uuid
     *
     * @return JsonResponse
     * @Route("/token/{uuid}", name="token_show", methods={"GET"})
     */
    public function showToken(TokenService $tokenService, string $uuid) : JsonResponse
    {
        return $tokenService->getToken($uuid);
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
     * @param TokenService $tokenService
     * @param string $uuid
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("/token/{uuid}", name="token_update", methods={"PUT"})
     */
    public function updateToken(TokenService $tokenService, string $uuid) : JsonResponse
    {
        return $tokenService->updateToken($uuid);
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
     * @param TokenService $tokenService
     * @param string $uuid
     *
     * @return JsonResponse
     * @Route("/token/{uuid}", name="token_delete", methods={"DELETE"})
     */
    public function deleteToken(TokenService $tokenService, string $uuid) : JsonResponse
    {
        return $tokenService->deleteToken($uuid);
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
     * @param TokenService $tokenService
     *
     * @return JsonResponse
     * @throws Exception
     * @Route("/tokens", name="token_create", methods={"POST"})
     */
    public function createToken(TokenService $tokenService) : JsonResponse
    {
        return $tokenService->createToken();
    }
}

