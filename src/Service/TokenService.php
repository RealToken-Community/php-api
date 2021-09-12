<?php

namespace App\Service;

use App\Entity\Token;
use DateTime;
use DOMDocument;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService extends Service
{
    /**
     * Get list of tokens.
     *
     * @param array $credentials
     * @param bool $deprecated
     *
     * @return JsonResponse
     */
    public function getTokens(array $credentials, bool $deprecated = false): JsonResponse
    {
        $tokens = $this->em->getRepository(Token::class)->findAll();

        $result = [];
        foreach ($tokens as $token) {
            if (!($token instanceof Token)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Token not found');
            }

            $result[] = $token->__toArray($credentials);
        }

        $response = Response::HTTP_OK;

        if ($deprecated) {
            $response = Response::HTTP_MOVED_PERMANENTLY;
        }

        return new JsonResponse($result, $response);
    }

    /**
     * Get token by uuid.
     *
     * @param array $credentials
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function getToken(array $credentials, string $uuid): JsonResponse
    {
        $token = $this->em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!($token instanceof Token)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Token not found');
        }

        return new JsonResponse($token->__toArray($credentials), Response::HTTP_OK);
    }

    /**
     * Global token creation.
     *
     * @param array $dataJson
     * @param bool $deprecated
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function createToken(array $dataJson = [], bool $deprecated = false): JsonResponse
    {
        $count["create"] = $count["update"] = 0;
        $parsedJson = $this->checkAndParseDataJson($dataJson);

        if (!$parsedJson) {
            throw new HttpException(Response::HTTP_NOT_ACCEPTABLE, 'Data is empty or not recognized');
        }

        $tokenRepository = $this->em->getRepository(Token::class);

        // Check if unique value or multiple are push
        if (!isset($parsedJson[0])) { // Single
            /** @var Token $token */
            $token = $tokenRepository->findOneBy(['ethereumContract' => $parsedJson['ethereumContract']]);
            $this->createOrUpdateToken($token, $parsedJson, $count);
            $this->em->flush();
        } else { // Multiple
            $tokens = $tokenRepository->findBy(['ethereumContract' => array_column($parsedJson, 'ethereumContract')]);

            $this->em->getConnection()->beginTransaction();

            $batchSize = 50;
            $i = 1;
            foreach ($parsedJson as $item) {
                if (empty($item['ethereumContract'])) {
                    continue;
                }
                if (false === $this->haveValidChannel($item['canal'])) {
                    continue;
                }

                $token = array_filter($tokens, static function ($currentToken) use ($item) {
                    if ($currentToken->getEthereumContract() === $item['ethereumContract']) {
                        return true;
                    }
                });

                $this->createOrUpdateToken($token[array_key_first($token)] ?? null, $item, $count);

                ++$i;
                if ($i % $batchSize === 0) {
                    $this->em->flush();
                    $this->em->getConnection()->commit();
                    $this->em->getConnection()->beginTransaction();
                }
            }

            $this->em->flush();
            $this->em->getConnection()->commit();
        }

        $responseCode = Response::HTTP_CREATED;

        if ($deprecated) {
            $responseCode = Response::HTTP_MOVED_PERMANENTLY;
        }

        $message = $count["create"] . " tokens created & " . $count["update"] . " updated successfully";

        return new JsonResponse(
            ["status" => "success", "message" => $message],
            $responseCode
        );
    }

    private function updateExistingToken(Token $token, array $parsedJson): void
    {
        $this->updateTokenData($token, $parsedJson);
    }

    private function updateTokenData(Token $token, array $parsedJson): void
    {
        if (isset($parsedJson[0])) {
            $parsedJson = $parsedJson[0];
        }

//        if (empty($token->getSymbol())) {
//            if ($symbol = $this->getRealtokenSymbol($token->getEthereumContract())) {
//                $token->setSymbol($symbol);
//            }
//        }

        // Check if secondaryMarketplaces is different
        $hasMpModified = $this->checkMarketplacesDifference($token, $parsedJson);

        $this->tokenMapping($parsedJson, $token);

        if ($hasMpModified) {
            // Get xDai Contract
            $secondaryMarketplaces = $token->getOriginSecondaryMarketplaces();

            foreach ($secondaryMarketplaces as $key => $secondaryMarketplace) {
                if (array_key_exists("pair", $secondaryMarketplace)) {
                    continue;
                }
                $chainName = strtolower($secondaryMarketplace["chainName"]);

                // Tmp xDaiChain fix
                // TODO : Add enum and chainId behind chainName
                if ($chainName === "xdaichain"
                    || $chainName === "ethereum") {
                    $pairToken = $this->getLpPairToken($chainName, $secondaryMarketplace["contractPool"], $token->getSymbol());
                    if (!empty($pairToken)) {
                        $secondaryMarketplaces[$key]["pair"] = $pairToken;
                        $token->setSecondaryMarketplaces($secondaryMarketplaces);
                    }
                }
            }
        }
    }

    /**
     * Update token from uuid.
     *
     * @param string $uuid
     * @param array|null $dataJson
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateToken(string $uuid, array $dataJson = []): JsonResponse
    {
        $token = $this->checkTokenExistence($uuid);

        $parsedJson = $this->checkAndParseDataJson($dataJson);

        if (!$parsedJson) {
            throw new HttpException(Response::HTTP_NOT_ACCEPTABLE, 'Data is empty or not recognized');
        }

        $this->updateTokenData($token, $parsedJson);

        $this->em->persist($token);
        $this->em->flush();

        return new JsonResponse(
            ["status" => "success", "message" => "Token updated successfully"],
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Delete token from uuid.
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function deleteToken(string $uuid): JsonResponse
    {
        $token = $this->checkTokenExistence($uuid);

        $this->em->remove($token);
        $this->em->flush();

        return new JsonResponse(
            ["status" => "success", "message" => "Token deleted successfully"],
            Response::HTTP_OK
        );
    }

    /**
     * Check existence of Token.
     *
     * @param string $uuid
     *
     * @return Token
     */
    private function checkTokenExistence(string $uuid): Token
    {
        /** @var Token $token */
        $token = $this->em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!$token) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Record doesn\'t exist');
        }

        return $token;
    }

    /**
     * Check difference from Json secondaryMarketplaces and token data.
     *
     * @param $token
     * @param $json
     *
     * @return bool
     */
    private function checkMarketplacesDifference($token, $json): bool
    {
        $hashSource = md5(serialize($json['secondaryMarketplaces']));
        $hashOrigin = md5(serialize($token->getOriginSecondaryMarketplaces()));
        $hashWithPair = md5(serialize($token->getSecondaryMarketplaces()));

        if (!hash_equals($hashSource, $hashOrigin) || hash_equals($hashOrigin, $hashWithPair)) {
            return true;
        }

        return false;
    }

    /**
     * Check and parse body data.
     *
     * @param array $dataJson
     *
     * @return bool|array
     */
    private function checkAndParseDataJson(array $dataJson = [])
    {
        if (array_keys($dataJson)[0] === "fullName") {
            $newData = [];
            if ($this->haveValidChannel($dataJson['canal'])) {
                $newData = $dataJson;
            }
            return $newData;
        }

        if (array_keys($dataJson)[0] === "tokens") {
            $newData = [];
            $data = $dataJson['tokens'];
            foreach ($data as $value) {
                if ($this->haveValidChannel($value['canal'])) {
                    $newData[] = $value;
                }
            }
            return $newData;
        }

        if (array_key_first($dataJson[0]) === "fullName") {
            $newData = [];
            foreach ($dataJson as $value) {
                if ($this->haveValidChannel($value['canal'])) {
                    $newData[] = $value;
                }
            }
            return $newData;
        }

        return false;
    }

    /**
     * @param Token|null $actualToken
     * @param array $parsedJson
     * @param array $count
     * @throws Exception
     */
    private function createOrUpdateToken(?Token $actualToken, array $parsedJson, array &$count): void
    {
        if ($actualToken) { // UPDATE
            $this->updateExistingToken($actualToken, $parsedJson);
            $this->em->persist($actualToken);
            ++$count['update'];
        } else { // CREATE
            $token = $this->tokenMapping($parsedJson);

            $token->setSecondaryMarketplaces($token->getOriginSecondaryMarketplaces());

//            if ($symbol = $this->getRealtokenSymbol($token->getEthereumContract())) {
//                $token->setSymbol($symbol);
//            }

            $this->em->persist($token);
            ++$count['create'];
        }
    }

    /**
     * Check channel validity.
     *
     * @param $channel
     *
     * @return bool
     */
    private function haveValidChannel($channel): bool
    {
        return $channel === Token::CANAL_RELEASE || $channel === Token::CANAL_COMING_SOON;
    }

    /**
     * Get LP pair tokens from Blockscout.
     *
     * @param $network
     * @param $contractAddress
     * @param $tokenSymbol
     *
     * @return array
     */
    private function getLpPairToken($network, $contractAddress, $tokenSymbol): array
    {
        if ($network === "ethereum") {
            $uri = "https://api.etherscan.io/api?module=account&action=tokentx&address=".$contractAddress."&sort=asc";
        } else {
            $uri = "https://blockscout.com/xdai/mainnet/api?module=account&action=tokentx&address=".$contractAddress."&sort=asc";
        }
        $json = $this->curlRequest($uri);

        $response = json_decode($json, true);

        // Ignore error & UniswapV1
        if (empty($response)
            || $response["status"] === "0"
            || $response["result"][0]["hash"] != $response["result"][1]["hash"]) {
            return [];
        }

        if ($response["result"][0]["tokenSymbol"] !== $tokenSymbol) {
            $index = 0;
        } else {
            $index = 1;
        }

        $lpPair["contract"] = $response["result"][$index]["contractAddress"];
        $lpPair["symbol"] = $response["result"][$index]["tokenSymbol"];
        $lpPair["name"] = $response["result"][$index]["tokenName"];

        return $lpPair;
    }

    /**
     * Make cURL request.
     *
     * @param $uri
     *
     * @return bool|string
     */
    // TODO : Fix same function from DefiService
    private function curlRequest($uri)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $uri,
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

        return $response;
    }

    /**
     * Build token skeleton.
     *
     * @param array $dataJson
     * @param Token|null $token
     *
     * @return Token
     * @throws Exception
     */
    private function tokenMapping(array $dataJson, $token = null): Token
    {
        if (!$token) {
            $token = new Token();
        }
        $token->setFullName((string)$dataJson['fullName']);
        $token->setShortName($dataJson['shortName'] ?: null);
        $token->setTokenPrice((float)$dataJson['tokenPrice'] ?: null);
        $token->setCanal($dataJson['canal'] ?: null);
        $token->setCurrency($dataJson['currency'] ?: null);
        $token->setTotalTokens($dataJson['totalTokens'] ?: 0);
        $token->setEthereumContract($dataJson['ethereumContract']);
        $token->setMaticContract(isset($dataJson['maticContract']) ? $dataJson['maticContract'] : null);
        $token->setXDaiContract($dataJson['xDaiContract'] ?: null);
        $token->setTotalInvestment((float)$dataJson['totalInvestment'] ?: null);
        $token->setGrossRentMonth(isset($dataJson['grossRent']) ? (float)$dataJson['grossRent'] : $dataJson['grossRentMonth']);
        $token->setGrossRentYear($token->getGrossRentMonth() * 12?: null);
        $token->setPropertyManagementPercent((float)$dataJson['propertyManagementPercent'] ?: null);
        $token->setPropertyManagement(
            $token->getGrossRentMonth() * $token->getPropertyManagementPercent() ?: null
        );
        $token->setRealtPlatformPercent(isset($dataJson['realtPlatformPercent']) ? (float)$dataJson['realtPlatformPercent'] : null);
        $token->setRealtPlatform($token->getGrossRentMonth() * $token->getRealtPlatformPercent() ?: null);
        $token->setInsurance((float)$dataJson['insurance'] ?: null);
        $token->setPropertyTaxes((float)$dataJson['propertyTaxes'] ?: null);
        $token->setUtilities((float)$dataJson['utilities'] ?: null);
        $token->setPropertyMaintenanceMonthly((float)$dataJson['propertyMaintenanceMonthly'] ?: null);
        $token->setNetRentMonth(
            ($token->getGrossRentMonth()
            - $token->getPropertyManagement()
            - $token->getRealtPlatform()
            - $token->getPropertyTaxes()
            - $token->getInsurance()
            - $token->getUtilities()
            - $token->getPropertyMaintenanceMonthly()) ?: null);
        $token->setNetRentYear($token->getNetRentMonth() * 12 ?: null);
        $token->setNetRentDay($token->getNetRentYear() / 365 ?: null);
        $token->setNetRentYearPerToken($token->getNetRentYear() / $token->getTotalTokens() ?: null);
        $token->setNetRentMonthPerToken($token->getNetRentYearPerToken() / 12 ?: null);
        $token->setNetRentDayPerToken($token->getNetRentYearPerToken() / 365 ?: null);
        $token->setAnnualPercentageYield($token->getTotalInvestment()
            ? $token->getNetRentYear() / $token->getTotalInvestment() * 100
            : null
        );
        $token->setCoordinate([
            'lat' => number_format(floatval($dataJson['coordinate']['lat']), 6),
            'lng' => number_format(floatval($dataJson['coordinate']['lng']), 6)
        ] );
        $token->setMarketplaceLink($dataJson['marketplaceLink'] ?? null);
        $token->setImageLink($dataJson['imageLink']);
        $token->setPropertyType($dataJson['propertyType'] ?: null);
        $token->setSquareFeet($dataJson['squareFeet'] ?: null);
        $token->setLotSize($dataJson['lotSize'] ?: null);
        $token->setBedroomBath(!empty($dataJson['bedroom/bath']) ? $dataJson['bedroom/bath'] : null);
        $token->setHasTenants($dataJson['hasTenants'] ?: null);
        $token->setRentedUnits($dataJson['rentedUnits'] ?: null);
        $token->setTotalUnits($dataJson['totalUnits'] ?: null);
        $token->setTermOfLease($dataJson['termOfLease'] ?: null);
        $token->setRenewalDate(null);
        if (!is_array($dataJson['renewalDate'])) {
            $renewalDate = date_create_from_format('d\/m\/Y', $dataJson['renewalDate']);
            if ($renewalDate instanceof DateTime) {
                $token->setRenewalDate($renewalDate);
            }
        }
        $token->setSection8paid(isset($dataJson['section8paid']) ? $dataJson['section8paid'] : null);
        $token->setSellPropertyTo($dataJson['sellPropertyTo'] ?: null);
        $token->setSecondaryMarketplace($dataJson['secondaryMarketplace'] ?? null);
        $token->setOriginSecondaryMarketplaces(
            !empty($dataJson['secondaryMarketplaces'])
            ? $dataJson['secondaryMarketplaces']
            : []);
        $token->setBlockchainAddresses(
            !empty($dataJson['blockchainAddresses'])
            ? $dataJson['blockchainAddresses']
            : null);
        $token->setUnderlyingAssetPrice((float)$dataJson['underlyingAssetPrice'] ?: null);
        $token->setRenovationReserve((float)$dataJson['renovationReserve'] ?: null);
        $token->setRentStartDate(!is_array($dataJson['rentStartDate']) ? new DateTime($dataJson['rentStartDate']) : null);
        $token->setInitialMaintenanceReserve($dataJson['initialMaintenanceReserve'] ?: null);
        $token->setLastUpdate(new DateTime());

        return $token;
    }
}
