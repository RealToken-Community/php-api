<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait DataControllerTrait
{
  /**
   * Parse Json from request.
   *
   * @param Request $request
   *
   * @return array $dataJson
   */
  public function getDataJson(Request $request): array
  {
    return json_decode($request->getContent(), true);
  }

  /**
   * Get request referer.
   *
   * @param Request $request
   *
   * @return string|null
   */
  public function getReferer(Request $request): ?string
  {
    return $request->headers->get('referer') ?? null;
  }

  /**
   * Extract domain Uri.
   *
   * @param string $uri
   *
   * @return string|null
   */
  private function extractDomainUri(string $uri): ?string
  {
    $pattern = "/^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n?]+)/";
    preg_match($pattern, $uri, $matches);

    return !empty($matches[1]) ? $matches[1] : null;
  }
}
