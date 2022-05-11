<?php

namespace App\Controller;

use App\Service\AuthenticatorService;
use App\Service\UserService;
use App\Traits\DataControllerTrait;
use App\Traits\HeadersControllerTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    use HeadersControllerTrait;
    use DataControllerTrait;

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
     * @Route("/admin/register/apiUser", name="register_api_user", methods={"GET", "POST"})
     */
    public function registerApiUser(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

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
}
