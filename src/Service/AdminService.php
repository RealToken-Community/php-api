<?php

namespace App\Service;

use App\Repository\QuotaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminService
 * @package App\Service
 */
class AdminService
{
    private $entityManager;
    protected $request;

    /**
     * AdminService constructor.
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

        $quotaRepository = $this->entityManager->getRepository(QuotaRepository::class);
        $usersQuota = $quotaRepository->getTotalUsersQuota();

        $response->setData($usersQuota)
            ->setStatusCode(Response::HTTP_OK);
        return $response;
    }

}