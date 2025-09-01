<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Application;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class RequestContextService
{
    public function __construct(
        private RequestStack $requestStack,
        private Security     $security
    ) {}

    public function getApplication(): ?Application
    {
        return $this->requestStack->getCurrentRequest()?->attributes->get('api_application');
    }

    public function isAuthenticated(): bool
    {
        return (bool) $this->security->getUser();
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->security->getUser()?->getRoles() ?? [], true);
    }

    public function isHydrator(): bool
    {
        return in_array('ROLE_HYDRATOR', $this->security->getUser()?->getRoles() ?? [], true);
    }

    public function getCurrentUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    public function getRateLimiterIdentifier(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->security->getUser();

        if ($user && method_exists($user, 'getUserIdentifier')) {
            return $user->getUserIdentifier();
        }

        return $request?->getClientIp() ?? 'anonymous';
    }
}
