<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Token;
use App\Entity\User;
use App\Security\TokenAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use DOMDocument;
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
     * @return array|JsonResponse
     */
    public function checkCredentials()
    {
        $response = new JsonResponse();

        $em = $this->entityManager;
        $request = $this->request;

        $apiKey = $request->headers->get('X-AUTH-REALT-TOKEN');
        $credentials = ['isAdmin' => false];

        if (!empty($apiKey)) {
            $applicationRepository = $em->getRepository(Application::class);
            $application = $applicationRepository->findOneBy(['apiToken' => $apiKey]);

            if (!($application Instanceof Application)) {
                $response->setData(["status" => "error", "message" => "Token is not recognized"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            $user = $application->getUser();
            $roles = $user->getRoles();

            if (in_array("ROLE_ADMIN", $roles)) {
                $credentials = ['isAdmin' => true];
            }

            $tokenAuthenticator = new TokenAuthenticator($this->entityManager);
            $isAuth = $tokenAuthenticator->supports($request);

            if (!$isAuth) {
                $response->setData(["status" => "error", "message" => "Invalid API Token"])
                        ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            $quotaService = new QuotaService($em);
            $quotaService->consumeQuota($application);

            $credentials['isAuth'] = true;
            return $credentials;
        }
        $credentials['isAuth'] = false;
        return $credentials;
    }

    /**
     * Get list of tokens.
     *
     * @return array|JsonResponse
     */
    public function getTokens()
    {
        $response = new JsonResponse();

        $credentials = $this->checkCredentials();
        if ($credentials Instanceof JsonResponse) {
            return $credentials;
        }

        $isAuth = $credentials['isAuth'] ?? false;
        $isAdmin = $credentials['isAdmin'] ?? false;

        $em = $this->entityManager;
        $tokens = $em->getRepository(Token::class)->findAll();

        $result = [];
        foreach ($tokens as $token){
            if (!($token instanceof Token)) {
                $response->setData(["status" => "error", "message" => "Token not found"])
                        ->setStatusCode(Response::HTTP_NOT_FOUND);
                return $response;
            }

            $result[] = $token->__toArray($isAuth, $isAdmin);
        }

        $response->setData($result)
            ->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * Get token by uuid.
     *
     * @param string $uuid
     *
     * @return array|JsonResponse
     */
    public function getToken(string $uuid)
    {
        $response = new JsonResponse();

        $credentials = $this->checkCredentials();
        if ($credentials Instanceof JsonResponse) {
            return $credentials;
        }

        $isAuth = $credentials['isAuth'] ?? false;
        $isAdmin = $credentials['isAdmin'] ?? false;

        $em = $this->entityManager;

        $token = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!($token instanceof Token)) {
            $response->setData(['status' => 'error', 'message' => 'Token not found'])
                    ->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }

        $response->setData($token->__toArray($isAuth, $isAdmin))
                ->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * Update token from uuid.
     *
     * @param string $uuid
     * @param array|null $dataJson
     *
     * @return JsonResponse
     */
    public function updateToken(string $uuid, array $dataJson = [])
    {
        $response = new JsonResponse();

        $credentials = $this->checkCredentials();
        if ($credentials Instanceof JsonResponse) {
            return $credentials;
        }

        $isAuth = $credentials['isAuth'] ?? false;
        $isAdmin = $credentials['isAdmin'] ?? false;
        if (!$isAuth) {
            $response->setData(["status" => "error", "message" => "Authentication Required"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        } elseif (!$isAdmin) {
            $response->setData(["status" => "error", "message" => "User is not granted"])
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        $em = $this->entityManager;
        $token = $this->checkTokenExistence($em, $uuid);

        if (!($token Instanceof Token)) {
            return $token;
        }

        $parsedJson = $this->checkAndParseDataJson($dataJson);

        if (!$parsedJson) {
            $response->setData(["status" => "error", "message" => "Data is empty or not recognized"])
                ->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
        }

        if (isset($parsedJson[0])) {
            $parsedJson = $parsedJson[0];
        }

        $this->tokenMapping($parsedJson, $token);
        $em->flush();

        $response->setData(["status" => "success", "message" => "Token updated successfully"])
                ->setStatusCode(Response::HTTP_ACCEPTED);
        return $response;
    }

    /**
     * Delete token from uuid.
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function deleteToken(string $uuid)
    {
        $response = new JsonResponse();

        $credentials = $this->checkCredentials();
        if ($credentials Instanceof JsonResponse) {
            return $credentials;
        }

        $isAuth = $credentials['isAuth'] ?? false;
        $isAdmin = $credentials['isAdmin'] ?? false;
        if (!$isAuth) {
            $response->setData(["status" => "error", "message" => "Authentication Required"])
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        } elseif (!$isAdmin) {
            $response->setData(["status" => "error", "message" => "User is not granted"])
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

        $credentials = $this->checkCredentials();
        if ($credentials Instanceof JsonResponse) {
            return $credentials;
        }

        $isAuth = $credentials['isAuth'] ?? false;
        $isAdmin = $credentials['isAdmin'] ?? false;
        if (!$isAuth) {
            $response->setData(["status" => "error", "message" => "Authentication Required"])
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        } elseif (!$isAdmin) {
            $response->setData(["status" => "error", "message" => "User is not granted"])
                ->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        $em = $this->entityManager;

        $parsedJson = $this->checkAndParseDataJson();

        if (!$parsedJson) {
            $response->setData(["status" => "error", "message" => "Data is empty or not recognized"])
                    ->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            return $response;
        }

        // Check if unique value or multiple are push
        if (!isset($parsedJson[0])) {
            $actualToken = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $parsedJson['ethereumContract']]);
            if ($actualToken instanceof Token) { // UPDATE
                $token = $this->tokenMapping($parsedJson);
                $response = $this->updateToken($token->getEthereumContract(), $parsedJson);
            } else { // CREATE
                $token = $this->tokenMapping($parsedJson);
                $symbol = $this->getRealtokenSymbol($token->getEthereumContract());
                if ($symbol) {
                    $token->setSymbol($symbol);
                }
                $em->persist($token);

                $response->setData(["status" => "success", "message" => "Token created successfully"])
                        ->setStatusCode(Response::HTTP_CREATED);
            }
        } else {
            foreach ($parsedJson as $item){
                if (empty($item['ethereumContract'])) throw new Exception("Field ethereumContract is empty !");
                if ($item['canal'] === "Alpha") continue;

                $tokenRepository = $em->getRepository(Token::class);

                $actualToken = $tokenRepository->findOneBy(['ethereumContract' => $item['ethereumContract']]);
                if ($actualToken instanceof Token) { // UPDATE
                    $token = $this->tokenMapping($item);
                    $response = $this->updateToken($token->getEthereumContract(), $item);
                } else { // CREATE
                    $token = $this->tokenMapping($item);
                    $symbol = $this->getRealtokenSymbol($token->getEthereumContract());
                    if ($symbol) {
                        $token->setSymbol($symbol);
                    }
                    $em->persist($token);

                    $response->setData(["status" => "success", "message" => "Token created successfully"])
                            ->setStatusCode(Response::HTTP_CREATED);
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

        return $response;
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
     *
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
     * Check and parse body data.
     *
     * @param array $dataJson
     *
     * @return bool|array
     */
    private function checkAndParseDataJson(array $dataJson = [])
    {
        if (empty($dataJson) && empty($this->getDataJson())) {
            return false;
        } elseif (empty($dataJson)) {
            $dataJson = $this->getDataJson();
            if (empty($dataJson)) {
                return false;
            }
        }

        if (array_keys($dataJson)[0] === "fullName") {
            $newData = [];
            if ($dataJson["canal"] === "Release") {
                $newData = $dataJson;
            }
            return $newData;
        } elseif (array_keys($dataJson)[0] === "tokens") {
            $newData = [];
            $data = $dataJson['tokens'];
            foreach ($data as $key => $value) {
                if ($value['canal'] === "Release") {
                    $newData[] = $value;
                }
            }
            return $newData;
        } else {
            return false;
        }
    }

    /**
     * Get symbol token from EtherscanDOM.
     *
     * @param $ethereumContract
     *
     * @return false|mixed
     */
    private function getRealtokenSymbol($ethereumContract) {
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
     */
    private function tokenMapping(array $dataJson, $token = null): Token
    {
        if (!$token) {
            $token = new Token();
        }
        $token->setFullName((string)$dataJson['fullName']);
        $token->setShortName($dataJson['shortName'] ?? null);
        $token->setTokenPrice($dataJson['tokenPrice'] ?? null);
        $token->setCanal($dataJson['canal'] ?? null);
        $token->setCurrency($dataJson['currency'] ?? null);
        $token->setTotalTokens($dataJson['totalTokens'] ?? null);
        $token->setEthereumContract($dataJson['ethereumContract']);
        $token->setMaticContract($dataJson['maticContract'] ?? null);
        $token->setAssetPrice($dataJson['assetPrice'] ?? null);
        $token->setGrossRentMonth($dataJson['grossRent'] ?? null);
        $token->setGrossRentYear($token->getGrossRentMonth() * 12 ?? null);
        $token->setPropertyManagementPercent($dataJson['propertyManagementPercent'] ?? null);
        $token->setPropertyManagement($token->getGrossRentMonth() * $token->getPropertyManagementPercent() ?? null);
        $token->setRealtPlatformPercent($dataJson['realTPlatformPercent'] ?? null);
        $token->setRealtPlatform($token->getGrossRentMonth() * $token->getRealtPlatformPercent() ?? null);
        $token->setInsurance($dataJson['insurance'] ?? null);
        $token->setPropertyTaxes($dataJson['propertyTaxes'] ?? null);
        $token->setUtilities($dataJson['utilities'] ?? null);
        $token->setPropertyMaintenance($dataJson['propertyMaintenance'] ?? null);
        $token->setNetRentMonth(
            $token->getGrossRentMonth()
            - $token->getPropertyManagement()
            - $token->getRealtPlatform()
            - $token->getPropertyTaxes()
            - $token->getInsurance() ?? null);
        $token->setNetRentYear($token->getNetRentMonth() * 12 ?? null);
        $token->setNetRentDay($token->getNetRentYear() / 365 ?? null);
        $token->setNetRentYearPerToken($token->getNetRentYear() / $token->getTotalTokens() ?? null);
        $token->setNetRentMonthPerToken($token->getNetRentYearPerToken() / 12 ?? null);
        $token->setNetRentDayPerToken($token->getNetRentYearPerToken() / 365 ?? null);
        $token->setAnnualPercentageYield($token->getNetRentYear() / $token->getAssetPrice() * 100 ?? null);
        $token->setCoordinate([
            'lat' => number_format(floatval($dataJson['coordinate']['lat']), 6),
            'lng' => number_format(floatval($dataJson['coordinate']['lng']), 6)
        ] );
        $token->setMarketplaceLink($dataJson['marketplace'] ?? null);
        $token->setImageLink($dataJson['imageLink']);
        $token->setPropertyType($dataJson['propertyType'] ?? null);
        $token->setSquareFeet($dataJson['squareFeet'] ?? null);
        $token->setLotSize($dataJson['lotSize'] ?? null);
        $token->setBedroomBath($dataJson['bedroom/bath'] ?? null);
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
        $token->setBlockchainAddresses($dataJson['blockchainAddresses'] ?? null);
        $token->setLastUpdate(new DateTime());

        return $token;
    }
}