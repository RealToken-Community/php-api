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

        // TODO : future list
//        $ammList = $this->tokenListMapping();

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

    /**
     * Token list mapping.
     *
     * @return array
     */
    private function tokenListMapping()
    {
        return [
            "name"      =>	"RealToken",
            "logoURI"   =>	"https://realt.co/wp-content/uploads/2019/01/cropped-RealToken_Favicon.png",
            "timestamp" =>	new \DateTime(),
            "version"   => [
                "major" => 2,
                "minor" => 0,
                "patch" => 0
            ],
            "keywords" => [
                0 => "Uniswap",
                1 => "DeFi",
                2 => "RealT",
                3 => "Ethereum",
                4 => "xDai chain",
            ],
            "tags" => [
                "ETH" => [
                    "name"          => "Ethereum",
                    "description"   => "RealToken on Ethereum mainnet, chainID 1"
                ],
                "MATIC" => [
                    "name"          => "Matic network",
                    "description"   => "RealToken on Matic mainnet, chainID 137"
                ],
                "mumbai" => [
                    "name"          => "Mumbai Testnet",
                    "description"   => "RealToken on Mumbai Testnet, chainID 80001"
                ],
                "xDai" => [
                    "name"          => "xDai chain",
                    "description"   => "RealToken on xDai mainnet, chainID 100"
                ],
                "sokol" => [
                    "name"          => "Sokol Testnet",
                    "description"   => "RealToken on Sokol Testnet, chainID ....."
                ],
                "gorli" => [
                    "name"          => "Goerli TestNet",
                    "description"   => "RealToken on Goerli Testnet, chainID 5"
                ],
                "stablecoin" => [
                    "name"          => "Stable coin",
                    "description"   => "Stablecoin, crypto with a stable value often equivalent to US Dollars"
                ],
                "aave" => [
                    "name"          => "Stable coin Aave",
                    "description"   => "Stablecoin Aave, crypto with a stable value often equivalent to US dollars and which generates interest via the Aave platform"
                ],
                "uniV1" => [
                    "name"          => "Uniswap V1",
                    "description"   => "Available on Uniswap V1"
                ],
                "uniV2" => [
                    "name"          => "Uniswap V2",
                    "description"   => "Available on Uniswap V2"
                ],
                "honey" => [
                    "name"          => "HoneySwap",
                    "description"   => "Available on HoneySwap"
                ],
                "levin" => [
                    "name"          => "LevinSwap",
                    "description"   => "Available on LevinSwap"
                ],
                "pairLevin" => [
                    "name"          => "LEVIN pair",
                    "description"   => "The available peer for this pool is RealToken and LEVIN"
                ],
                "SF" => [
                    "name"          => "Single family",
                    "description"   => "RealT Single family property"
                ],
                "MF" => [
                    "name"          => "Multi family",
                    "description"   => "RealT Multi family property"
                ],
                "2020" => [
                    "name"          => "Sold in 2020",
                    "description"   => "House tokenizer and put up for sale by RealT in 2020"
                ],
                "2021" => [
                    "name"          => "Sold in 2021",
                    "description"   => "House tokenizer and put up for sale by RealT in 2021"
                ]
            ],
        ];
    }
}
