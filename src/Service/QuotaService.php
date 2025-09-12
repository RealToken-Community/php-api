<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class QuotaService
 * @package App\Service
 */
class QuotaService extends Service
{
  private array $limiters = [];

  public function __construct(
    iterable $limiters
  ) {
    $this->limiters = [];
    foreach ($limiters as $name => $limiter) {
      $key = str_replace('limiter.', '', $name);
      $this->limiters[$key] = $limiter;
    }
  }

  public function getUserQuotas(Request $request, RequestContextService $ctx): array
  {
    $roles = $ctx->getCurrentUser()?->getRoles() ?? ['ANONYMOUS'];
    $role = $this->extractMainRole($roles);

    $identifier = $ctx->getRateLimiterIdentifier();

    $periods = ['minute', 'hour', 'day', 'week', 'month', 'year'];
    $quotas = [];

    foreach ($periods as $period) {
      $key = $role . '_' . $period;

      if (!isset($this->limiters[$key])) {
        continue;
      }

      $limiter = $this->limiters[$key]->create($identifier);
      $limit = $limiter->consume(0);

      $quotas[$period] = [
        'remaining' => $limit->getRemainingTokens(),
        'limit' => $limit->getLimit(),
        'retry_after' => $limit->getRetryAfter()?->format(DATE_ATOM),
      ];
    }

    return $quotas;
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
}
