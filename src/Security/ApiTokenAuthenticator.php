<?php

namespace App\Security;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly ApplicationRepository $applicationRepository
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-REALT-TOKEN')
            || $request->query->has('realtAuthToken');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-REALT-TOKEN')
            ?? $request->query->get('realtAuthToken');

        if (!$apiToken) {
            throw new AuthenticationException('Missing API token.');
        }

        $application = $this->applicationRepository->findOneBy(['apiToken' => $apiToken]);

        if (!$application instanceof Application) {
            throw new AuthenticationException('Invalid API token.');
        }

        $expectedReferer = $application->getReferer();
        $actualReferer = $request->headers->get('referer');

        if (!empty($expectedReferer) && $actualReferer !== $expectedReferer) {
            throw new AuthenticationException('Invalid referer source.');
        }

        $request->attributes->set('api_application', $application);

        $identifier = $application->getUser()->getUserIdentifier()
            ?? $request->getClientIp();

        return new SelfValidatingPassport(
            new UserBadge($identifier, fn() => $application->getUser())
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['error' => 'Authentication Failed'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
