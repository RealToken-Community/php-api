<?php

namespace App\Controller;

use App\Service\DefiService;
use App\Service\RequestContextService;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserService $userService;
    private DefiService $defiService;

    public function __construct(
        UserService $userService,
        DefiService $defiService
    ) {
        $this->userService = $userService;
        $this->defiService = $defiService;
    }

    /**
     * Register new Api User.
     *
     * @param Request $request
     * @param RequestContextService $ctx
     * @return Response
     * @throws Exception
     */
    #[Route("/admin/register/apiUser", name: 'register_api_user', methods: ['GET', 'POST'])]
    public function registerApiUser(Request $request, RequestContextService $ctx): Response
    {
        $form = [];
        if ($request->getMethod() == 'POST') {
            $form = $this->userService->userRegistration($request);
        }

        return $this->render(
            "admin/registerApiUser.html.twig", [
                'apiKey' => $ctx->getApplication()->getApiToken(),
                'form' => $form,
            ]
        );
    }
}
