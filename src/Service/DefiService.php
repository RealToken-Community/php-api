<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefiService
 * @package App\Service
 */
class DefiService extends Service
{
    /**
     * Generate token list for AMM.
     *
     * @param string|null $refer
     *
     * @return JsonResponse
     */
    public function getTokenListForAMM(?string $refer): JsonResponse
    {
        $ammList = $this->getCommunityList($refer);

        return new JsonResponse($ammList, Response::HTTP_OK);
    }

    /**
     * Get AMM community list DOM.
     *
     * @param string|null $refer
     *
     * @return array
     */
    public function getCommunityList(?string $refer): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://realt.ch/tokensListes/?referer=" . $refer,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
