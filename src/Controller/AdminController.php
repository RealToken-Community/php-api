<?php

namespace App\Controller;

use App\Service\AdminService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/admin")
 */
class AdminController
{
    /**
     * Admin get Users Quota.
     *
     * @param AdminService $adminService
     *
     * @return JsonResponse
     * @Route("/getUsersQuota", name="admin-user-quota", methods={"GET"})
     */
    public function getUsersQuota(AdminService $adminService): JsonResponse
    {
        return $adminService->getTotalUsersQuota();
    }
}
