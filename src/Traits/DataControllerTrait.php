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
     * Get request refer.
     *
     * @param Request $request
     *
     * @return string|null
     */
    public function getRefer(Request $request): ?string
    {
        return $request->headers->get('referer') ?? null;
    }
}
