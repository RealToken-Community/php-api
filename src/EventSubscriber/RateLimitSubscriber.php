<?php

namespace App\EventSubscriber;

use App\Service\RequestContextService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;

class RateLimitSubscriber implements EventSubscriberInterface
{
  private array $limiters;
  private Security $security;

  public function __construct(
    private readonly RequestContextService $requestContextService,
    iterable $limiters,
    Security $security
  ) {
    foreach ($limiters as $name => $limiter) {
      $this->limiters[$name] = $limiter;
    }

    $this->security = $security;
  }

  public function onKernelRequest(RequestEvent $event): void
  {
    $request = $event->getRequest();
    $path = $request->getPathInfo();

    $excludedPaths = [
      '/v1/quota',
      '/v1/status',
      '/v1/ping',
    ];

    $request = $event->getRequest();
    if (!str_starts_with($request->getPathInfo(), '/v1')) {
      return;
    }

    if (in_array($path, $excludedPaths, true)) {
      return;
    }

    $routeName = $request->attributes->get('_route');
    $controller = $request->attributes->get('_controller');

    if (empty($routeName) || $controller === 'error_controller') {
      return;
    }

    if (str_starts_with($routeName, '_profiler') || str_starts_with($routeName, '_wdt')) {
      return;
    }

    $roles = $this->security->getUser()?->getRoles() ?? ['ANONYMOUS'];

    $identifier = $this->requestContextService->getRateLimiterIdentifier();

    $role = $this->extractMainRole($roles);

    $periods = ['minute', 'hour', 'day', 'week', 'month', 'year'];

    $failedLimit = null;

    $limits = [];

    foreach ($periods as $period) {
      $key = strtolower($role) . '_' . $period;
      if (!isset($this->limiters[$key])) {
        continue;
      }

      $limit = $this->limiters[$key]->create($identifier)->consume(1);
      $limits[$period] = $limit;

      if (!$limit->isAccepted()) {
        $failedLimit = [$period, $limit];
        break;
      }
    }

    if ($failedLimit) {
      [$period, $limit] = $failedLimit;
      throw new TooManyRequestsHttpException(
        $limit->getRetryAfter()?->getTimestamp() - time(),
        "Quota $period exceeded."
      );
    }

    foreach ($limits as $limit) {
      $limit->ensureAccepted();
    }
  }

  private function extractMainRole(array $roles): string
  {
    $priority = [
      'ROLE_ADMIN',
      'ROLE_VIP',
      'ROLE_EXTERNAL',
      'ROLE_PREMIUM',
      'ROLE_FREEMIUM',
    ];

    foreach ($priority as $role) {
      if (in_array($role, $roles, true)) {
        return strtolower(str_replace('ROLE_', '', $role));
      }
    }

    return 'anonymous';
  }

  public static function getSubscribedEvents(): array
  {
    return [KernelEvents::REQUEST => 'onKernelRequest'];
  }
}
