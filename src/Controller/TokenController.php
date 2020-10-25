<?php

namespace App\Controller;

use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1")
 */
class TokenController
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * List all tokens.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of tokens",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param Request $request
     * @return JsonResponse
     * @Route("/tokens", name="tokens_show", methods={"GET"})
     */
    public function showTokens(Request $request): JsonResponse
    {
        $tokenService = new TokenService($request, $this->entityManager);
        $tokens = $tokenService->getTokens();

        return new JsonResponse($tokens);
    }

    /**
     * Return data from token.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return data from token",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     * @Route("/token/{uuid}", name="token_show", methods={"GET"})
     */
    public function showToken(string $uuid, Request $request) : JsonResponse
    {
        $tokenService = new TokenService($request, $this->entityManager);
        $token = $tokenService->getToken($uuid);

        return new JsonResponse($token);
    }

    /**
     * Update token data.
     *
     * @OA\Response(
     *     response=200,
     *     description="Update token data",
     * )
     * @OA\Parameter(
     *     name="data",
     *     in="query",
     *     description="JSON data token",
     *     @OA\Schema(type="json")
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     * @Route("/token/{uuid}", name="token_update", methods={"PUT"})
     */
    public function updateToken(string $uuid, Request $request) : JsonResponse
    {
        $tokenService = new TokenService($request, $this->entityManager);
        $tokenService->updateToken($uuid);

        return new JsonResponse(["status" => "success", "message" => "updated"], Response::HTTP_CREATED);
    }

    /**
     * Delete token.
     *
     * @OA\Response(
     *     response=200,
     *     description="Delete token",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     * @Route("/token/{uuid}", name="token_delete", methods={"DELETE"})
     */
    public function deleteToken(string $uuid, Request $request) : JsonResponse
    {
        $tokenService = new TokenService($request, $this->entityManager);
        $tokenService->deleteToken($uuid);

        return new JsonResponse(["status" => "success", "message" => "deleted"], Response::HTTP_CREATED);
    }

    /**
     * Create token data.
     *
     * @OA\Response(
     *     response=200,
     *     description="Create token data",
     * )
     * @OA\Parameter(
     *     name="data",
     *     in="query",
     *     description="JSON data token",
     *     @OA\Schema(type="json")
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param Request $request
     * @return JsonResponse
     * @Route("/tokens", name="token_create", methods={"POST"})
     */
    public function createToken(Request $request) : JsonResponse
    {
        $tokenService = new TokenService($request, $this->entityManager);
        $tokenService->createToken();

        return new JsonResponse(["status" => "success", "message" => "created or updated"], Response::HTTP_CREATED);
    }
}

