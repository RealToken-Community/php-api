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
     * Get request origin.
     *
     * @param Request $request
     *
     * @return string|null
     */
    public function getRequestOrigin(Request $request): ?string
    {
        return $request->server->get('HTTP_ORIGIN') ?? null;
    }

    /**
     * Get request ip address.
     *
     * @param Request $request
     *
     * @return string|null
     */
    public function getRequestIpAddress(Request $request): ?string
    {
        return $request->server->get('HTTP_X_REAL_IP') ?? null;
    }
}
