<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private ApplicationRepository $applicationRepository;

    public function __construct(ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-REALT-TOKEN');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $apiToken = $this->getApiToken($request);
        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $user = $this->getUser($apiToken);
        return new SelfValidatingPassport(new UserBadge($user->getEmail()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Get API token from request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    private function getApiToken(Request $request): ?string
    {
        $headerParam = $request->headers->get('X-AUTH-REALT-TOKEN');
        $queryParam = $request->query->get('realtAuthToken');

        if (is_null($headerParam) && is_null($queryParam)){
            return null;
        }

        return (!is_null($headerParam) ? $headerParam : $queryParam);
    }

    /**
     * Get User from API token.
     *
     * @param string $apiToken
     *
     * @return User|null
     */
    private function getUser(string $apiToken): ?User
    {
        $application = $this->applicationRepository->findOneBy(['apiToken' => $apiToken]);

        return $application->getUser();
    }
}
