<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\UserService;
use App\Traits\HeadersControllerTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    use HeadersControllerTrait;

    /** @var AuthenticatorService */
    private AuthenticatorService $authenticatorService;
    /** @var UserService */
    private UserService $userService;

    public function __construct(AuthenticatorService $authenticatorService, UserService $userService)
    {
        $this->authenticatorService = $authenticatorService;
        $this->userService = $userService;
    }

    /**
     * Register new Api User.
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     * @Route("/admin/users/register", name="register_api_user", methods={"GET", "POST"})
     */
    public function registerApiUser(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights($apiKey);

        $form = [];
        if ($request->getMethod() == 'POST') {
            $form = $this->userService->userRegistration($request);
        }

        return $this->render(
            "admin/registerApiUser.html.twig", [
                'apiKey' => $apiKey,
                'form' => $form,
            ]
        );
    }

    /**
     * Register new Api User.
     *
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     * @Route("/admin/users/manage", name="manage_api_user", methods={"GET", "POST"})
     */
    public function manageApiUser(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights($apiKey);

        $form = [];
//        if ($request->getMethod() == 'POST') {
//            $form = $this->userService->userRegistration($request);
//        }

        return $this->render(
            "admin/manageApiUser.html.twig", [
                'apiKey' => $apiKey,
                'users' => $this->userService->getUsers(),
            ]
        );
    }
}
