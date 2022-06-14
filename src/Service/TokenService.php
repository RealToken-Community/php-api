<?php

namespace App\Service;

use App\Entity\Token;
use App\Traits\NetworkControllerTrait;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService extends Service
{
    use NetworkControllerTrait;

    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheInterface $cache)
    {
        $this->cache = $cache;

        parent::__construct($entityManager);
    }

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

        // Todo : Factorize previous and next rqt
        if (empty($token)) {
          $token = $this->em->getRepository(Token::class)->findOneBy(['xDaiContract' => $uuid]);
        }

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
            $token = $this->checkTokenExistence($parsedJson['uuid']);

            $this->createOrUpdateToken($token, $parsedJson, $count);
            $this->em->flush();
        } else { // Multiple
            $tokens = $tokenRepository->findBy(['uuid' => array_column($parsedJson, 'uuid')]);

            $this->em->getConnection()->beginTransaction();

            $batchSize = 50;
            $i = 1;
            foreach ($parsedJson as $item) {
                if (empty($item['uuid'])) {
                    continue;
                }

                if (false === $this->haveValidChannel($item['canal'])) {
                    continue;
                }

                $token = array_filter($tokens, static function ($currentToken) use ($item) {
                    /** @var Token $currentToken */
                    if (strtolower($currentToken->getUuid()) === strtolower($item['uuid'])) {
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

    /**
     * Update token from uuid.
     *
     * @param string $contractAddress
     * @param array|null $dataJson
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateToken(string $contractAddress, array $dataJson = []): JsonResponse
    {
        $token = $this->checkTokenExistence($contractAddress);

        $parsedJson = $this->checkAndParseDataJson($dataJson);

        if (!$parsedJson) {
            throw new HttpException(Response::HTTP_NOT_ACCEPTABLE, 'Data is empty or not recognized');
        }

        if (!$this->diffTokenApiWithJson($token, $parsedJson)) {
            return new JsonResponse(
                ["status" => "success", "message" => "Nothing to update"],
                Response::HTTP_ACCEPTED
            );
        }

        $this->tokenMapping($parsedJson, $token);

        $this->em->persist($token);
        $this->em->flush();

        return new JsonResponse(
            ["status" => "success", "message" => "Token updated successfully"],
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Delete token from contract address.
     *
     * @param string $contractAddress
     *
     * @return JsonResponse
     */
    public function deleteToken(string $contractAddress): JsonResponse
    {
        $token = $this->checkTokenExistence($contractAddress);

        $this->em->remove($token);
        $this->em->flush();

        return new JsonResponse(
            ["status" => "success", "message" => "Token deleted successfully"],
            Response::HTTP_OK
        );
    }

    /**
     * Show latest token updated.
     *
     * @param array $credentials
     * @return JsonResponse
     */
    public function showLatestUpdated(array $credentials): JsonResponse
    {
        $tokenRepository = $this->em->getRepository(Token::class);

        /** @var Token $lastTokenUpdated */
        $lastTokenUpdated = $tokenRepository->getLastTokenUpdated()[0];

        return new JsonResponse($lastTokenUpdated->__toArray($credentials), Response::HTTP_OK);
    }

    /**
     * Show latest token update time.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function showLatestUpdateTime(): JsonResponse
    {
        $tokenRepository = $this->em->getRepository(Token::class);

        $lastUpdateTime = new DateTime($tokenRepository->getLastTokenUpdateTime());

        return new JsonResponse($lastUpdateTime, Response::HTTP_OK);
    }

    /**
     * Check existence of Token.
     *
     * @param string $contractAddress
     *
     * @return Token
     */
    private function checkTokenExistence(string $contractAddress): Token
    {
        //$uuid = $this->getUuidByUniversalId($contractAddress);

        /** @var Token $token */
        $token = $this->em->getRepository(Token::class)->findOneBy(['uuid' => $contractAddress]);

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
            $this->tokenMapping($parsedJson, $actualToken);
            $this->em->persist($actualToken);
            ++$count['update'];
        } else { // CREATE
            $token = $this->tokenMapping($parsedJson);

            $token->setSecondaryMarketplaces($token->getOriginSecondaryMarketplaces());

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
     * Get token uuid from contracts.
     *
     * @param string $uuid
     *
     * @return string
     */
    private function getUuidByUniversalId(string $uuid): string
    {
        if (!empty($tokenByUuid = $this->em->getRepository(Token::class)->findOneBy(['uuid' => $uuid]))) {
            $uuid = $tokenByUuid["uuid"];
        } elseif (!empty($tokenByGnosisContract = $this->em->getRepository(Token::class)->findOneBy(['gnosisContract' => $uuid]))) {
            $uuid = $tokenByGnosisContract["uuid"];
        } elseif (!empty($tokenByXdaiContract = $this->em->getRepository(Token::class)->findOneBy(['xDaiContract' => $uuid]))) {
            $uuid = $tokenByXdaiContract["uuid"];
        } elseif (!empty($tokenByEthereumContract = $this->em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]))) {
            $uuid = $tokenByEthereumContract["uuid"];
        } else {
            $uuid = null;
        }

        return $uuid;
    }

    /**
     * Compare API Token with Json token.
     *
     * @param Token $token
     * @param array $json
     *
     * @return bool
     */
    private function diffTokenApiWithJson(Token $token, array $json): bool
    {
        $hashToken = $this->hashTokenData($token);
        $hashJson = $this->hashTokenData($json);

        if ($hashToken != $hashJson) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Hash token data.
     *
     * @param $token
     *
     * @return string
     */
    private function hashTokenData($token): string
    {
        $result = $this->getTokenProperties($token);
        $hash = hash("md5", (string)$result);

        return $hash;
    }

    private function getTokenProperties($json): array
    {
        if (gettype($json) === "object") {
            $toto = json_encode($json);
            $err = json_last_error_msg();
            $array = (array)$json;

            $result = [];
            foreach ($json as $key => $value)
            {
                $result[$key] = (is_array($value) || is_object($value)) ? object_to_array($value) : $value;
            }

            $json = json_decode(json_encode($json),true);
        }

        return [
            $json['assetParking'],
            $json['bedroom/bath'],
            $json['blockchainAddresses'],
            $json['canal'],
            $json['constructionType'],
            $json['constructionYear'],
            $json['cooling'],
            $json['currency'],
            $json['ethereumContract'],
            $json['foundation'],
            $json['fullName'],
            $json['hasTenants'],
            $json['heating'],
            $json['imageLink'],
            $json['initialLaunchDate'],
            $json['initialMaintenanceReserve'],
            $json['lotSize'],
            $json['marketplaceLink'],
            $json['propertyStories'],
            $json['propertyType'],
            $json['renewalDate'],
            $json['rentCalculationType'],
            $json['rentStartDate'],
            $json['rentedUnits'],
            $json['roofType'],
            $json['secondaryMarketplace'],
            $json['secondaryMarketplaces'],
            $json['sellPropertyTo'],
            $json['seriesNumber'],
            $json['shortName'],
            $json['squareFeet'],
            $json['symbol'],
            $json['termOfLease'],
            $json['tokenIdRules'],
            $json['totalTokens'],
            $json['totalTokensRegSummed'],
            $json['totalUnits'],
            $json['uuid'],
            $json['xDaiContract'],
            (float)$json['grossRent'],
            (float)$json['insurance'],
            (float)$json['miscellaneousCosts'],
            (float)$json['propertyMaintenanceMonthly'],
            (float)$json['propertyManagementPercent'],
            (float)$json['propertyTaxes'],
            (float)$json['realtListingFee'],
            (float)$json['realtListingFeePercent'],
            (float)$json['realtPlatformPercent'],
            (float)$json['renovationReserve'],
            (float)$json['tokenPrice'],
            (float)$json['totalInvestment'],
            (float)$json['underlyingAssetPrice'],
            (float)$json['utilities'],
            (int)$json['section8paid']
        ];
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
    private function tokenMapping(array $dataJson, ?Token $token = null): Token
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
        $token->setTotalTokensRegSummed($dataJson['totalTokensRegSummed'] ?: 0);
        $token->setUuid($dataJson['uuid'] ?: null);
        $token->setEthereumContract($dataJson['ethereumContract'] ?: null);
        $token->setXDaiContract($dataJson['xDaiContract'] ?: null);
        $token->setGnosisContract($dataJson['xDaiContract'] ?: null);
        $token->setTotalInvestment((float)$dataJson['totalInvestment'] ?: null);
        $token->setGrossRentMonth(isset($dataJson['grossRent']) ? (float)$dataJson['grossRent'] : 0);
        $token->setGrossRentYear($token->getGrossRentMonth() * 12 ?: 0);
        $token->setPropertyManagementPercent((float)$dataJson['propertyManagementPercent'] ?: 0);
        $token->setPropertyManagement(
            $token->getGrossRentMonth() * $token->getPropertyManagementPercent() ?: 0
        );
        $token->setRealtPlatformPercent(isset($dataJson['realtPlatformPercent'])
            ? (float)$dataJson['realtPlatformPercent']
            : 0
        );
        $token->setRealtPlatform($token->getGrossRentMonth() * $token->getRealtPlatformPercent() ?: 0);
        $token->setInsurance((float)$dataJson['insurance'] ?: 0);
        $token->setPropertyTaxes((float)$dataJson['propertyTaxes'] ?: 0);
        $token->setUtilities((float)$dataJson['utilities'] ?: 0);
        $token->setPropertyMaintenanceMonthly((float)$dataJson['propertyMaintenanceMonthly'] ?: 0);
        $token->setNetRentMonth(0);
        $expenses = $token->getPropertyManagement()
            + $token->getRealtPlatform()
            + $token->getPropertyTaxes()
            + $token->getInsurance()
            + $token->getUtilities()
            + $token->getPropertyMaintenanceMonthly();
        if ($token->getGrossRentMonth() - $expenses > 0) {
            $token->setNetRentMonth(
                $token->getGrossRentMonth()
                - $expenses ?: 0);
        }
        $token->setNetRentYear($token->getNetRentMonth() * 12 ?: 0);
        $token->setNetRentDay($token->getNetRentYear() / 365 ?: 0);
        $token->setNetRentYearPerToken(
            !empty($token->getTotalTokensRegSummed())
            ? $token->getNetRentYear() / $token->getTotalTokensRegSummed()
            : $token->getNetRentYear() / $token->getTotalTokens());
        $token->setNetRentMonthPerToken($token->getNetRentYearPerToken() / 12 ?: 0);
        $token->setNetRentDayPerToken($token->getNetRentYearPerToken() / 365 ?: 0);
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
        $token->setRentedUnits($dataJson['rentedUnits'] ?: 0);
        $token->setTotalUnits($dataJson['totalUnits'] ?: null);
        $token->setTermOfLease($dataJson['termOfLease'] ?: null);
        $token->setRenewalDate(null);
        if (!is_array($dataJson['renewalDate'])) {
            $renewalDate = date_create_from_format('d\/m\/Y', $dataJson['renewalDate']);
            if ($renewalDate instanceof DateTime) {
                $token->setRenewalDate($renewalDate);
            }
        }
        $token->setSection8paid(isset($dataJson['section8paid']) ? (int)$dataJson['section8paid'] : null);
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
        $token->setRentStartDate(
            !is_array($dataJson['rentStartDate'])
            && !empty($dataJson['rentStartDate'])
                ? new DateTime($dataJson['rentStartDate'])
                : null);
        $token->setInitialMaintenanceReserve($dataJson['initialMaintenanceReserve'] ?: null);
        $token->setInitialLaunchDate(
            !is_array($dataJson['initialLaunchDate'])
            && !empty($dataJson['initialLaunchDate'])
                ? new DateTime($dataJson['initialLaunchDate'])
                : null);
        $token->setSeriesNumber($dataJson['seriesNumber'] ?: null);
        $token->setConstructionYear($dataJson['constructionYear'] ?: null);
        $token->setConstructionType($dataJson['constructionType'] ?: null);
        $token->setRoofType($dataJson['roofType'] ?: null);
        $token->setAssetParking($dataJson['assetParking'] ?: null);
        $token->setFoundation($dataJson['foundation'] ?: null);
        $token->setHeating($dataJson['heating'] ?: null);
        $token->setCooling($dataJson['cooling'] ?: null);
        $token->setTokenIdRules($dataJson['tokenIdRules'] ?: null);
        $token->setRentCalculationType($dataJson['rentCalculationType'] ?: null);
        if ($token->getSymbol() == null) {
            $token->setSymbol($dataJson['symbol'] ?: null);
        }
        $token->setRealtListingFeePercent((float)$dataJson['realtListingFeePercent'] ?: null);
        $token->setRealtListingFee((float)$dataJson['realtListingFee'] ?: null);
        $token->setMiscellaneousCosts((float)$dataJson['miscellaneousCosts'] ?: null);
        $token->setPropertyStories($dataJson['propertyStories'] ?: null);

        $token->setLastUpdate(new DateTime());

        return $token;
    }
}
