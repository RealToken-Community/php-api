<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Security\TokenAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1")
 */
class TokenController
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * List all tokens.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of tokens",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param Request $request
     * @return JsonResponse
     * @Route("/tokens", name="tokens_show", methods={"GET"})
     */
    public function showTokens(Request $request): JsonResponse
    {
        $tokenAuthenticator = new TokenAuthenticator($this->entityManager);
        $isAuth = $tokenAuthenticator->supports($request);

        /** @var Token $tokens */
        $tokens = $this->entityManager->getRepository(Token::class)->findAll();
        $response = [];
        foreach ($tokens as $token){
            $response[] = $token->__toArray($isAuth);
        }
        return new JsonResponse($response);
    }

    /**
     * Return data from token.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return data from token",
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param string $uuid
     * @param Request $request
     * @return JsonResponse
     * @Route("/token/{uuid}", name="token_show", methods={"GET"})
     */
    public function showToken(string $uuid, Request $request) : JsonResponse
    {
        $tokenAuthenticator = new TokenAuthenticator($this->entityManager);
        $isAuth = $tokenAuthenticator->supports($request);

        /** @var Token $token */
        $token = $this->entityManager->getRepository(Token::class)->findOneBy(
            ['ethereumContract' => $uuid]
        );
        if ($token instanceof Token){
            return new JsonResponse($token->__toArray($isAuth));
        }
        return new JsonResponse(['status' => 'error', 'message' => 'not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Create token data.
     *
     * @OA\Response(
     *     response=200,
     *     description="Create token data",
     * )
     * @OA\Parameter(
     *     name="data",
     *     in="query",
     *     description="JSON data token",
     *     @OA\Schema(type="json")
     * )
     * @OA\Tag(name="Tokens")
     * @Security(name="Header")
     * @param Request $request
     * @return JsonResponse
     * @Route("/tokens", name="token_create", methods={"POST"})
     */
    public function createToken(Request $request) : JsonResponse
    {
        $tokenAuthenticator = new TokenAuthenticator($this->entityManager);
        $isAuth = $tokenAuthenticator->supports($request);

        if (!$isAuth) {
            return new JsonResponse(["status" => "error", "message" => "Invalid API Token."],Response::HTTP_UNAUTHORIZED);
        } else {
            $userRepository = $this->entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['apiToken' => $request->headers->get('X-AUTH-REALT-TOKEN')]);
            $roles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $roles)) {
                return new JsonResponse(["status" => "error", "message" => "User is not granted."],Response::HTTP_FORBIDDEN);
            }
        }

        $dataJson = json_decode($request->getContent(), true);

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

        if (is_array($dataJson[0])){
            foreach ($dataJson as $item){
                if (empty($item['ethereumContract'])) throw new Exception("Field ethereumContract is empty !");
                if ($item['canal'] === "Alpha") continue;
                if (!$this->entityManager->getRepository(Token::class)->findOneBy(
                        ['ethereumContract' => $item['ethereumContract']]
                    ) instanceof Token) {
                    $token = $this->buildTokenObject($item);
                    $this->entityManager->persist($token);
                }
            }
        } else {
        if ($this->entityManager->getRepository(Token::class)->findOneBy(
                ['ethereumContract' => $dataJson['ethereumContract']]
            ) instanceof Token) {
            return new JsonResponse(['status' => 'success'], Response::HTTP_CREATED);
        }
        $token = $this->buildTokenObject($dataJson);
        $this->entityManager->persist($token);
    }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success'], Response::HTTP_CREATED);
    }

    /**
     * Build token skeleton.
     *
     * @param array $dataJson
     * @return Token
     */
    private function buildTokenObject(array $dataJson) : Token
    {
        $token = new Token();
        $token->setFullName((string)$dataJson['fullName']);
        $token->setShortName($dataJson['shortName']);
        $token->setTokenPrice($dataJson['tokenPrice']);
        $token->setPublicSale($dataJson['isPublicSale']);
        $token->setCanal($dataJson['canal']);
        $token->setCurrency($dataJson['currency']);
        $token->setTotalTokens($dataJson['totalTokens']);
        $token->setEthereumContract($dataJson['ethereumContract']);
        $token->setEthereumDistributor($dataJson['ethereumDistributor']);
        if (strlen($dataJson['ethereumMaintenance']) <= 42){
            $token->setEthereumMaintenance($dataJson['ethereumMaintenance']);
        }
        $token->setAssetPrice($dataJson['assetPrice']);
        $token->setGrossRent($dataJson['grossRent']);
        $token->setRentPerToken($dataJson['rentPerToken']);
        //$token->setPropertyManagementPercent($dataJson['propertyManagementPercent']);
        //$token->setRealtPlatformPercent($dataJson['realtPlatformPercent']);
        $token->setInsurance($dataJson['insurance']);
        $token->setPropertyTaxes($dataJson['propertyTaxes']);
        $token->setUtilities($dataJson['utilities']);
        $token->setPropertyMaintenance($dataJson['propertyMaintenance']);
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
        return $token;
    }
}

