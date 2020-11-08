<?php

namespace App\Service;

use App\Entity\Token;
use App\Entity\User;
use App\Security\TokenAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService
{
    const SIMPLE_FORMAT = "simple";
    const ADVANCED_FORMAT = "wordpress";

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;
    /** @var Request $request */
    private $request;

    /**
     * TokenService constructor.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Request $request, EntityManagerInterface $entityManager)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    /**
     * Check if user is auth.
     *
     * @return bool|JsonResponse
     */
    public function checkCredentials()
    {
        $response = new JsonResponse();

        $em = $this->entityManager;
        $request = $this->request;

        $apiKey = $request->headers->get('X-AUTH-REALT-TOKEN');

        if (!empty($apiKey)) {
            $userRepository = $em->getRepository(User::class);
            $user = $userRepository->findOneBy(['apiToken' => $apiKey]);
            $roles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $roles)) {
                $response->setData(["status" => "error", "message" => "User is not granted"])
                        ->setStatusCode(Response::HTTP_FORBIDDEN);
                return $response;
            }

            $tokenAuthenticator = new TokenAuthenticator($this->entityManager);
            $isAuth = $tokenAuthenticator->supports($request);

            if (!$isAuth) {
                $response->setData(["status" => "error", "message" => "Invalid API Token"])
                        ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            return true;
        }
        return false;
    }

    /**
     * Get list of tokens.
     *
     * @return array|JsonResponse
     */
    public function getTokens()
    {
        $response = new JsonResponse();

        $isAuth = $this->checkCredentials();
        if (!boolval($isAuth)) {
            return $isAuth;
        }

        $em = $this->entityManager;
        $tokens = $em->getRepository(Token::class)->findAll();

        foreach ($tokens as $token){
            if (!($token instanceof Token)) {
                $response->setData(["status" => "error", "message" => "Token not found"])
                        ->setStatusCode(Response::HTTP_NOT_FOUND);
                return $response;
            }

            $response->setData($token->__toArray($isAuth))
                    ->setStatusCode(Response::HTTP_OK);
        }

        return $response;
    }

    /**
     * Get token by uuid.
     *
     * @param string $uuid
     * @return array|JsonResponse
     */
    public function getToken(string $uuid)
    {
        $response = new JsonResponse();

        $isAuth = $this->checkCredentials();
        if (!boolval($isAuth)) {
            return $isAuth;
        }

        $em = $this->entityManager;

        $token = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!($token instanceof Token)) {
            $response->setData(['status' => 'error', 'message' => 'Token not found'])
                    ->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        $response->setData($token->__toArray($isAuth))
                ->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * Update token from uuid.
     *
     * @param string $uuid
     * @param array|null $dataJson
     * @return JsonResponse
     */
    public function updateToken(string $uuid, array $dataJson = [])
    {
        $response = new JsonResponse();

        if (!$this->checkCredentials()) {
            $response->setData(["status" => "error", "message" => "Authentication Required"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        $em = $this->entityManager;
        $token = $this->checkTokenExistence($em, $uuid);

        if (!($token Instanceof Token)) {
            return $token;
        }

        if (empty($dataJson)) {
            $dataJson = $this->getDataJson();
        }

        $this->tokenMapping($dataJson, $token);
        $em->flush();

        $response->setData(["status" => "success", "message" => "Token updated successfully"])
                ->setStatusCode(Response::HTTP_ACCEPTED);

        return $response;
    }

    /**
     * Delete token from uuid.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function deleteToken(string $uuid)
    {
        $response = new JsonResponse();

        if (!$this->checkCredentials()) {
            $response->setData(["status" => "error", "message" => "Authentication Required"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        $em = $this->entityManager;
        $token = $this->checkTokenExistence($em, $uuid);

        if (!($token Instanceof Token)) {
            return $token;
        }

        $em->remove($token);
        $em->flush();

        $response->setData(["status" => "success", "message" => "Token deleted successfully"])
                ->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    /**
     * Global token creation.
     *
     * @return JsonResponse
     */
    public function createToken()
    {
        $response = new JsonResponse();

        if (!$this->checkCredentials()) {
            $response->setData(["status" => "error", "message" => "Authentication Required"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        $em = $this->entityManager;

        $dataJson = $this->getDataJson();

        if (empty($dataJson)) {
            $response->setData(["status" => "error", "message" => "Data is empty"])
                    ->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            return $response;
        }

        $format = "";
        if (array_keys($dataJson)[0] === "fullName") {
            $format = self::SIMPLE_FORMAT;
        } elseif (array_keys($dataJson)[0] === "tokens") {
            $format = self::ADVANCED_FORMAT;
        }

        if ($format === self::SIMPLE_FORMAT) {
            $newData = [];
            if ($dataJson["canal"] === "Release") {
                $newData = $dataJson;
            }
            $dataJson = $newData;
        } elseif ($format === self::ADVANCED_FORMAT) {
            $newData = [];
            $data = $dataJson['tokens'];
            foreach ($data as $key => $value) {
                if ($value['canal'] === "Release") {
                    $newData[] = $value;
                }
            }
            $dataJson = $newData;
        }

        // Check if unique value or multiple are push
        if (!isset($dataJson[0])){
            $actualToken = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $dataJson['ethereumContract']]);
            if ($actualToken instanceof Token) { // UPDATE
                $token = $this->tokenMapping($dataJson);
                $this->updateToken($token->getEthereumContract(), $dataJson);

                $response->setData(["status" => "success", "message" => "Token updated successfully"])
                        ->setStatusCode(Response::HTTP_ACCEPTED);

                return $response;
            } else { // CREATE
                $token = $this->tokenMapping($dataJson);
                $em->persist($token);
            }
        } else {
            foreach ($dataJson as $item){
                if (empty($item['ethereumContract'])) throw new Exception("Field ethereumContract is empty !");
                if ($item['canal'] === "Alpha") continue;

                $tokenRepository = $em->getRepository(Token::class);

                $actualToken = $tokenRepository->findOneBy(['ethereumContract' => $item['ethereumContract']]);
                if ($actualToken instanceof Token) { // UPDATE
                    $token = $this->tokenMapping($item);
                    $this->updateToken($token->getEthereumContract(), $item);

                    $response->setData(["status" => "success", "message" => "Token updated successfully"])
                            ->setStatusCode(Response::HTTP_ACCEPTED);

                    return $response;
                } else { // CREATE
                    $token = $this->tokenMapping($item);
                    $em->persist($token);

                    $response->setData(["status" => "success", "message" => "Token created successfully"])
                            ->setStatusCode(Response::HTTP_CREATED);

                    return $response;
                }
            }
        }

        try {
            $em->flush();
        } catch (ORMException $e) {
            $response->setData(["status" => "error", "message" => $e])
                    ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $response;
        }
    }

    /**
     * Parse Json from request.
     *
     * @return array $dataJson
     */
    public function getDataJson()
    {
        $request = $this->request;

        return json_decode($request->getContent(), true);
    }

    /**
     * Check existence of Token.
     *
     * @param EntityManagerInterface $em
     * @param string $uuid
     * @return Token|JsonResponse
     */
    private function checkTokenExistence(EntityManagerInterface $em, string $uuid)
    {
        $response = new JsonResponse();

        $token = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!$token) {
            $response->setData(["status" => "error", "message" => "This record doesn't exist"])
                ->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        return $token;
    }

    /**
     * Build token skeleton.
     *
     * @param array $dataJson
     * @param Token|null $token
     * @return Token
     */
    private function tokenMapping(array $dataJson, $token = null): Token
    {
        if (!$token) {
            $token = new Token();
        }
        $token->setFullName((string)$dataJson['fullName']);
        $token->setShortName($dataJson['shortName']);
        $token->setTokenPrice($dataJson['tokenPrice']);
        $token->setCanal($dataJson['canal']);
        $token->setCurrency($dataJson['currency']);
        $token->setTotalTokens($dataJson['totalTokens']);
        $token->setEthereumContract($dataJson['ethereumContract']);
        $token->setEthereumDistributor($dataJson['ethereumDistributor']);
        if (strlen($dataJson['ethereumMaintenance']) <= 42) {
            $token->setEthereumMaintenance($dataJson['ethereumMaintenance']);
        }
        $token->setEthereumMaintenance($dataJson['ethereumMaintenance']);
        $token->setAssetPrice($dataJson['assetPrice']);
        $token->setGrossRentMonth($dataJson['grossRent']);
        $token->setGrossRentYear($token->getGrossRentMonth() * 12);
        $token->setPropertyManagementPercent($dataJson['propertyManagementPercent']);
        $token->setPropertyManagement($token->getGrossRentMonth() * $token->getPropertyManagementPercent());
        $token->setRealtPlatformPercent($dataJson['realTPlatformPercent']);
        $token->setRealtPlatform($token->getGrossRentMonth() * $token->getRealtPlatformPercent());
        $token->setInsurance($dataJson['insurance']);
        $token->setPropertyTaxes($dataJson['propertyTaxes']);
        $token->setUtilities($dataJson['utilities']);
        $token->setPropertyMaintenance($dataJson['propertyMaintenance']);
        $token->setNetRentMonth(
            $token->getGrossRentMonth()
            - $token->getPropertyManagement()
            - $token->getRealtPlatform()
            - $token->getPropertyTaxes()
            - $token->getInsurance());
        $token->setNetRentYear($token->getNetRentMonth() * 12);
        $token->setNetRentDay($token->getNetRentYear() / 365);
        $token->setNetRentYearPerToken($token->getNetRentYear() / $token->getTotalTokens());
        $token->setNetRentMonthPerToken($token->getNetRentYearPerToken() / 12);
        $token->setNetRentDayPerToken($token->getNetRentYearPerToken() / 365);
        $token->setAnnualPercentageYield($token->getNetRentYear() / $token->getAssetPrice() * 100);
        $token->setCoordinate([
            'lat' => number_format(floatval($dataJson['coordinate']['lat']), 6),
            'lng' => number_format(floatval($dataJson['coordinate']['lng']), 6)
        ] );
        $token->setMarketplaceLink($dataJson['marketplace']);
        $token->setImageLink($dataJson['imageLink']);
        $token->setPropertyType($dataJson['propertyType']);
        $token->setSquareFeet($dataJson['squareFeet']);
        if ($dataJson['lotSize'] === "") {
            $token->setLotSize(0);
        }
        $token->setBedroomBath($dataJson['bedroom/bath']);
        $token->setHasTenants($dataJson['hasTenants']);
        $token->setRentedUnits($dataJson['rentedUnits']);
        $token->setTotalUnits($dataJson['totalUnits']);
        $token->setTermOfLease($dataJson['termOfLease']);
        $renewalDate = date_create_from_format('d\/m\/Y', $dataJson['renewalDate']);
        if ($renewalDate instanceof DateTime) {
            $token->setRenewalDate($renewalDate);
        }
        if ($dataJson['section8paid'] === "") {
            $token->setSection8paid(0);
        }
        $token->setSellPropertyTo($dataJson['sellPropertyTo']);
        $token->setSecondaryMarketplace($dataJson['secondaryMarketPlace']);
        $token->setLastUpdate(new DateTime());

        return $token;
    }
}