<?php

namespace App\Controller;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TokenController
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/tokens", name="tokens_show", methods={"GET"})
     */
    public function showTokens(): JsonResponse
    {
        $tokens = $this->entityManager->getRepository(Token::class)->findAll();
        $response = [];
        foreach ($tokens as $token){
            $response[] = $token->__toArray();
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/tokens/{uuid}", name="token_show", methods={"GET"})
     */
    public function showToken(string $uuid) : JsonResponse
    {
        $token = $this->entityManager->getRepository(Token::class)->findOneBy(
            ['ethereumContract' => $uuid]
        );
        if ($token instanceof Token){
            return new JsonResponse($token->__toArray());
        }
        return new JsonResponse(['status' => 'error', 'message' => 'not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/tokens", name="token_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createToken(Request $request) : JsonResponse
    {
        $dataJson = json_decode($request->getContent(), true);
        if (is_array($dataJson[0])){
            foreach ($dataJson as $item){
                if (!$this->entityManager->getRepository(Token::class)->findOneBy(
                        ['ethereumContract' => $item['ethereumContract']]
                    ) instanceof Token) {
                    $token = $this->buildTokenObject($item);
                    $this->entityManager->persist($token);
                }
            }
            //return new JsonResponse();
        }else{
            if ($this->entityManager->getRepository(Token::class)->findOneBy(
                    ['ethereumContract' => $dataJson['ethereumContract']]
                ) instanceof Token) {
                return new JsonResponse(['status' => 'success'], Response::HTTP_CREATED);
            }
            $token = $this->buildTokenObject($dataJson);
            $this->entityManager->persist($token);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'successs'], Response::HTTP_CREATED);
    }

    /**
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
        $token->setLotSize($dataJson['lotSize']);
        $token->setBedroomBath($dataJson['bedroom/bath']);
        $token->setHasTenants($dataJson['hasTenants']);
        $token->setTermOfLease($dataJson['termOfLease']);
        $renewalDate = date_create_from_format('d\/m\/Y', $dataJson['renewalDate']);
        if ($renewalDate instanceof \DateTime){
            $token->setRenewalDate($renewalDate);
        }
        $token->setSection8paid($dataJson['section8paid']);
        $token->setSellPropertyTo($dataJson['sellPropertyTo']);
        $token->setOnUniswap($dataJson['onUniswap']);
        return $token;
    }
}

