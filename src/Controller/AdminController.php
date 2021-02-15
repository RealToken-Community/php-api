<?php

namespace App\Controller;

use App\Service\AdminService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/admin")
 */
class AdminController
{
    /**
     * RealToken list for AMM.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return list of RealToken for Automatic Market Maker",
     * )
     * @OA\Tag(name="DeFi")
     * @param AdminService $adminService
     *
     * @return JsonResponse
     * @Route("/tokenList", name="amm_list", methods={"GET"})
     */
    public function getUsersQuota(AdminService $adminService): JsonResponse
    {
        return $adminService->getTotalUsersQuota();
    }
}