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

class TokenService
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;
    /** @var Request $request */
    private $request;

    public function __construct(Request $request, EntityManagerInterface $entityManager)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    public function checkCredentials()
    {
        $em = $this->entityManager;
        $request = $this->request;

        $token = $request->headers->get('X-AUTH-REALT-TOKEN');

        if (!empty($token)) {
            $userRepository = $em->getRepository(User::class);
            $user = $userRepository->findOneBy(['apiToken' => $token]);
            $roles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $roles)) {
                return new JsonResponse(["status" => "error", "message" => "User is not granted."], Response::HTTP_FORBIDDEN);
            }

            $tokenAuthenticator = new TokenAuthenticator($this->entityManager);
            $isAuth = $tokenAuthenticator->supports($request);

            if (!$isAuth) {
                return new JsonResponse(["status" => "error", "message" => "Invalid API Token."], Response::HTTP_UNAUTHORIZED);
            }

            return true;
        }
        return false;
    }

    public function getTokens()
    {
        $isAuth = $this->checkCredentials();

        $em = $this->entityManager;

        $tokens = $em->getRepository(Token::class)->findAll();

        $response = [];
        foreach ($tokens as $token){
            if (!($token instanceof Token)) {
                return new JsonResponse(['status' => 'error', 'message' => 'not found'], Response::HTTP_NOT_FOUND);
            }

            $response[] = $token->__toArray($isAuth);
        }

        return $response;
    }

    public function getToken(string $uuid)
    {
        $isAuth = $this->checkCredentials();

        $em = $this->entityManager;

        $token = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!($token instanceof Token)) {
            return new JsonResponse(['status' => 'error', 'message' => 'not found'], Response::HTTP_NOT_FOUND);
        }

        return $token->__toArray($isAuth);
    }

    public function updateToken(string $uuid, ?array $dataJson)
    {
        $this->checkCredentials();

        $em = $this->entityManager;

        /** @var Token $token */
        $token = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!$token) {
            return new JsonResponse(["status" => "error", "message" => "This record doesn't exist."],Response::HTTP_NOT_FOUND);
        }

        if (empty($dataJson)) {
            $dataJson = $this->getDataJson();
        }

        $this->tokenMapping($dataJson, $token);

        $em->flush();
    }

    public function deleteToken(string $uuid)
    {
        $this->checkCredentials();

        $em = $this->entityManager;

        $token = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $uuid]);

        if (!$token) {
            return new JsonResponse(["status" => "error", "message" => "This record doesn't exist."],Response::HTTP_NOT_FOUND);
        }

        $em->remove($token);
        $em->flush();
    }

    public function createToken()
    {
        $this->checkCredentials();

        $em = $this->entityManager;

        $dataJson = $this->getDataJson();

        // Format array from WP/TM
        if (array_keys($dataJson)[0] === "tokens") {
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
        if (!is_array($dataJson[0])){
            $actualToken = $em->getRepository(Token::class)->findOneBy(['ethereumContract' => $dataJson['ethereumContract']]);
            if ($actualToken instanceof Token) { // UPDATE
                $token = $this->tokenMapping($dataJson);
                $this->updateToken($token->getEthereumContract(), $dataJson[0]);
                return new JsonResponse(["status" => "success", "message" => "Updated successfully"], Response::HTTP_CREATED);
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
                    return new JsonResponse(["status" => "success", "message" => "Updated successfully"], Response::HTTP_CREATED);
                } else { // CREATE
                    $token = $this->tokenMapping($item);
                    $em->persist($token);
                }
            }
        }

        try {
            $em->flush();
        } catch (ORMException $e) {
            return new JsonResponse(["status" => "error", "message" => $e],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return array $dataJson
     */
    public function getDataJson()
    {
        $request = $this->request;

        return json_decode($request->getContent(), true);
    }

    /**
     * Build token skeleton.
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
        $token->setPublicSale($dataJson['publicSale']);
        $token->setCanal($dataJson['canal']);
        $token->setCurrency($dataJson['currency']);
        $token->setTotalTokens($dataJson['totalTokens']);
        $token->setEthereumContract($dataJson['ethereumContract']);
        $token->setEthereumDistributor($dataJson['ethereumDistributor']);
        if (strlen($dataJson['ethereumMaintenance']) <= 42){
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
        $token->setNetRentYearPerToken($token->getNetRentYear() / $token->getTotalTokens());
        $token->setNetRentMonthPerToken($token->getNetRentYearPerToken() / 12);
        $token->setAnnualPercentageYield($token->getNetRentYear() / $token->getAssetPrice() * 100);
        $token->setCoordinate([
            'lat' => number_format(floatval($dataJson['coordinate']['lat']), 6),
            'lng' => number_format(floatval($dataJson['coordinate']['lng']), 6)
        ] );
        $token->setMarketplaceLink($dataJson['marketplace']);
        $token->setImageLink($dataJson['imageLink']);
        $token->setPropertyType($dataJson['propertyType']);
        $token->setSquareFeet($dataJson['squareFeet']);
        if ($dataJson['lotSize'] === ""){
            $token->setLotSize(0);
        }
        $token->setBedroomBath($dataJson['bedroom/bath']);
        $token->setHasTenants($dataJson['hasTenants']);
        $token->setTermOfLease($dataJson['termOfLease']);
        $renewalDate = date_create_from_format('d\/m\/Y', $dataJson['renewalDate']);
        if ($renewalDate instanceof DateTime){
            $token->setRenewalDate($renewalDate);
        }
        if ($dataJson['section8paid'] === ""){
            $token->setSection8paid(0);
        }
        $token->setSellPropertyTo($dataJson['sellPropertyTo']);
        $token->setOnUniswap($dataJson['onUniswap']);
        $token->setLastUpdate(new DateTime());

        return $token;
    }
}