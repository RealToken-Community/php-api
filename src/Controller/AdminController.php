<?php

namespace App\Controller;

use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * Admin get Users Quota.
     *
     * @param AdminService $adminService
     *
     * @return Response
     * @Route("/getUsersQuota", name="admin-user-quota", methods={"GET"})
     */
    public function getUsersQuota(AdminService $adminService): Response
    {
        $usersQuota = $adminService->getTotalUsersQuota();

        if (empty($usersQuota)) {
            return new Response();
        }

        return $this->render(
            "admin/usersQuota.html.twig", [
                'usersQuota' => $usersQuota,
            ]
        );
    }
}
