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
     *
     * @return JsonResponse
     */
    public function getTokens(array $credentials): JsonResponse
    {
        $tokens = $this->em->getRepository(Token::class)->findAll();

        $result = [];
        foreach ($tokens as $token) {
            if (!($token instanceof Token)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Token not found');
            }

            $result[] = $token->__toArray($credentials);
        }

        return new JsonResponse($result,Response::HTTP_OK);
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
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function createToken(array $dataJson = []): JsonResponse
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
        } else { // Multiple
            foreach ($parsedJson as $item) {
                if (empty($item['ethereumContract'])) continue;
                if (!$this->haveValidChannel($item['canal'])) continue;

                /** @var Token $token */
                $token = $tokenRepository->findOneBy(['ethereumContract' => $item['ethereumContract']]);
                $this->createOrUpdateToken($token, $item, $count);
            }
        }

        $this->em->flush();

        $message = $count["create"] . " tokens created & ". $count["update"] ." updated successfully";
        return new JsonResponse(
            ["status" => "success", "message" => $message],
            Response::HTTP_CREATED
        );
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

        if (isset($parsedJson[0])) {
            $parsedJson = $parsedJson[0];
        }

        if (empty($token->getSymbol())) {
            if ($symbol = $this->getRealtokenSymbol($token->getEthereumContract())) {
                $token->setSymbol($symbol);
            }
        }

        $this->tokenMapping($parsedJson, $token);
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
        } elseif (array_keys($dataJson)[0] === "tokens") {
            $newData = [];
            $data = $dataJson['tokens'];
            foreach ($data as $key => $value) {
                if ($this->haveValidChannel($value['canal'])) {
                    $newData[] = $value;
                }
            }
            return $newData;
        } elseif (array_key_first($dataJson[0]) === "fullName") {
            $newData = [];
            foreach ($dataJson as $key => $value) {
                if ($this->haveValidChannel($value['canal'])) {
                    $newData[] = $value;
                }
            }
            return $newData;
        } else {
            return false;
        }
    }

    /**
     * @param Token|null $actualToken
     * @param array $parsedJson
     * @param array $count
     * @throws Exception
     */
    private function createOrUpdateToken(?Token $actualToken, array $parsedJson, array &$count)
    {
        // UPDATE
        if ($actualToken instanceof Token) {
            $token = $this->tokenMapping($parsedJson);
            $this->updateToken($token->getEthereumContract(), $parsedJson);
            $count['update'] += 1;
        } // CREATE
        else {
            $token = $this->tokenMapping($parsedJson);

            if ($symbol = $this->getRealtokenSymbol($token->getEthereumContract())) {
                $token->setSymbol($symbol);
            }

            $this->em->persist($token);
            $count['create'] += 1;
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
        if ($channel === Token::CANAL_RELEASE || $channel === Token::CANAL_COMING_SOON) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get symbol token from EtherscanDOM.
     *
     * @param $ethereumContract
     *
     * @return false|string
     */
    private function getRealtokenSymbol($ethereumContract)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://etherscan.io/token/".$ethereumContract,
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

        $doc = new DOMDocument();
        @$doc->loadHTML($response);

        $title = $doc->getElementsByTagName('title');
        $title = $title->item(0)->textContent;

        if ($title === "Etherscan Error Page") {
            return false;
        }

        preg_match("/\((.*)\)/", $title, $symbol);
        $name = $symbol[1];

        $validSymbol = strpos($name, "REALTOKEN-");

        if (!$name || $validSymbol !== 0) {
            return false;
        }

        return $name;
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
        $token->setShortName($dataJson['shortName'] ?? null);
        $token->setTokenPrice((float)$dataJson['tokenPrice'] ?? null);
        $token->setCanal($dataJson['canal'] ?? null);
        $token->setCurrency($dataJson['currency'] ?? null);
        $token->setTotalTokens($dataJson['totalTokens'] ?? null);
        $token->setEthereumContract($dataJson['ethereumContract']);
        $token->setMaticContract($dataJson['maticContract'] ?? null);
        $token->setXDaiContract(!empty($dataJson['xDaiContract']) ?? null);
        $token->setTotalInvestment((float)$dataJson['totalInvestment'] ?? null);
        $token->setGrossRentMonth(round((float)$dataJson['grossRent'], 2) ?? null);
        $token->setGrossRentYear($token->getGrossRentMonth() * 12 ?? null);
        $token->setPropertyManagementPercent((float)$dataJson['propertyManagementPercent'] ?? null);
        $token->setPropertyManagement(
            round($token->getGrossRentMonth() * $token->getPropertyManagementPercent(), 2)
            ?? null
        );
        $token->setRealtPlatformPercent((float)$dataJson['realTPlatformPercent'] ?? null);
        $token->setRealtPlatform(
            round($token->getGrossRentMonth() * $token->getRealtPlatformPercent(), 2)
            ?? null
        );
        $token->setInsurance(round((float)$dataJson['insurance'], 2) ?? null);
        $token->setPropertyTaxes(round((float)$dataJson['propertyTaxes'], 2) ?? null);
        $token->setUtilities((float)$dataJson['utilities'] ?? null);
        $token->setNetRentMonth(round(
            $token->getGrossRentMonth()
            - $token->getPropertyManagement()
            - $token->getRealtPlatform()
            - $token->getPropertyTaxes()
            - $token->getInsurance()
            - $token->getUtilities()
            - $token->getPropertyMaintenanceMonthly(), 2) ?? null);
        $token->setNetRentYear(round($token->getNetRentMonth() * 12, 2) ?? null);
        $token->setNetRentDay($token->getNetRentYear() / 365 ?? null);
        $token->setNetRentYearPerToken(
            round($token->getNetRentYear() / $token->getTotalTokens(), 2)
            ?? null
        );
        $token->setNetRentMonthPerToken(
            round($token->getNetRentYearPerToken() / 12, 2)
            ?? null
        );
        $token->setNetRentDayPerToken(
            round($token->getNetRentYearPerToken() / 365, 2)
            ?? null
        );
        $token->setAnnualPercentageYield($token->getTotalInvestment()
            ? round($token->getNetRentYear() / $token->getTotalInvestment() * 100, 2)
            : null
        );
        $token->setCoordinate([
            'lat' => number_format(floatval($dataJson['coordinate']['lat']), 6),
            'lng' => number_format(floatval($dataJson['coordinate']['lng']), 6)
        ] );
        $token->setMarketplaceLink($dataJson['marketplace'] ?? null);
        $token->setImageLink($dataJson['imageLink']);
        $token->setPropertyType($dataJson['propertyType'] ?? null);
        $token->setSquareFeet($dataJson['squareFeet'] ?? null);
        $token->setLotSize($dataJson['lotSize'] ?? null);
        $token->setBedroomBath(!empty($dataJson['bedroom/bath']) ?: null);
        $token->setHasTenants($dataJson['hasTenants'] ?? null);
        $token->setRentedUnits($dataJson['rentedUnits'] ?? null);
        $token->setTotalUnits($dataJson['totalUnits'] ?? null);
        $token->setTermOfLease($dataJson['termOfLease'] ?? null);
        $renewalDate = date_create_from_format('d\/m\/Y', $dataJson['renewalDate']);
        if ($renewalDate instanceof DateTime) {
            $token->setRenewalDate($renewalDate);
        }
        $token->setSection8paid($dataJson['section8paid'] ?? null);
        $token->setSellPropertyTo($dataJson['sellPropertyTo'] ?? null);
        $token->setSecondaryMarketplace($dataJson['secondaryMarketPlace'] ?? null);
        $token->setSecondaryMarketplaces(
            !empty($dataJson['secondaryMarketPlaces'])
            || strlen($dataJson['secondaryMarketPlaces']) > 5
            ? $dataJson['secondaryMarketPlaces']
            : null);
        $token->setBlockchainAddresses(
            !empty($dataJson['blockchainAddresses'])
            || strlen($dataJson['blockchainAddresses']) > 5
            ? $dataJson['blockchainAddresses']
            : null);
        $token->setUnderlyingAssetPrice((float)$dataJson['underlyingAssetPrice'] ?? null);
        $token->setRenovationReserve((float)$dataJson['renovationReserve'] ?? null);
        $token->setPropertyMaintenanceMonthly((float)$dataJson['propertyMaintenanceMonthly'] ?? null);
        $token->setRentStartDate($dataJson['rentStartDate'] ? new DateTime($dataJson['rentStartDate']) : null);
        $token->setLastUpdate(new DateTime());

        return $token;
    }
}
