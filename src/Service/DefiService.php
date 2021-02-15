<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefiService
 * @package App\Service
 */
class DefiService
{
    private $entityManager;
    protected $request;

    /**
     * DefiService constructor.
     *
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->entityManager = $entityManager;
    }

    /**
     * Generate token list for AMM.
     *
     * @return JsonResponse
     */
    public function getTokenListForAMM()
    {
        $response = new JsonResponse();

        $ammList = $this->getCommunityList();

        $response->setData($ammList)
            ->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * Get AMM community list DOM.
     *
     * @return false|mixed
     */
    public function getCommunityList()
    {
        $refer = $this->request->headers->get('referer');

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