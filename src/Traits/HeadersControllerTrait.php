<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait HeadersControllerTrait
{
    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function getApiToken(Request $request): ?string
    {
        $headerParam = $request->headers->get('X-AUTH-REALT-TOKEN');
        $queryParam = $request->query->get('realtAuthToken');

        if (is_null($headerParam) && is_null($queryParam)){
//            throw new HttpException(401, "Bad Request");
            return null;
        }

        return (!is_null($headerParam) ? $headerParam : $queryParam);
    }
}
