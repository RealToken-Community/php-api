<?php

namespace App\Controller;

use App\Service\AdminService;
use App\Service\AuthenticatorService;
use App\Traits\DataControllerTrait;
use App\Traits\HeadersControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    use HeadersControllerTrait;
    use DataControllerTrait;

    /** @var AuthenticatorService */
    private AuthenticatorService $authenticatorService;
    /** @var AdminService */
    private AdminService $adminService;

    public function __construct(AuthenticatorService $authenticatorService, AdminService $adminService)
    {
        $this->authenticatorService = $authenticatorService;
        $this->adminService = $adminService;
    }

    /**
     * Admin get Users Quota.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/getUsersQuota", name="admin_user_quota", methods={"GET"})
     */
    public function getUsersQuota(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        return $this->render(
            "admin/usersQuota.html.twig", [
                'apiKey' => $apiKey,
                'usersQuota' => $this->adminService->getTotalUsersQuota(),
            ]
        );
    }

    /**
     * Manage quota (V1).
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/manageQuota", name="admin_manage_quota", methods={"GET", "POST"})
     */
    public function manageQuota(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        switch ($request->get('method')) {
            case 'create':
                $this->adminService->createQuotaLimitations($request);
                break;
            case 'update':
                $this->adminService->updateQuotaLimitations($request);
                break;
            case 'delete':
                $this->adminService->deleteQuotaLimitations($request);
                break;
        }

        return $this->render(
            "admin/quotaLimitations.html.twig", [
                'apiKey' => $apiKey,
                'quotas' => $this->adminService->getQuotaLimitations(),
            ]
        );
    }

    /**
     * Manage quotas (V2).
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/manageQuotas", name="admin_manage_quotas", methods={"GET", "POST"})
     */
    public function manageQuotas(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        switch ($request->get('method')) {
            case 'create':
                $this->adminService->createQuotaManagement($request);
                break;
            case 'update':
                $this->adminService->updateQuotaManagement($request);
                break;
            case 'delete':
                $this->adminService->deleteQuotaManagement($request);
                break;
        }

        return $this->render(
            "admin/quotaManagement.html.twig", [
                'apiKey' => $apiKey,
                'quotas' => $this->adminService->getQuotaManagement(),
            ]
        );
    }


    /**
     * Manage token mapping.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/manageTokenMapping", name="admin_manage_token_mapping", methods={"GET", "POST"})
     */
    public function manageTokenMapping(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        switch ($request->get('method')) {
            case 'create':
                $this->adminService->createTokenMapping($request);
                break;
            case 'update':
                $this->adminService->updateTokenMapping($request);
                break;
            case 'delete':
                $this->adminService->deleteTokenMapping($request);
                break;
        }

        return $this->render(
            "admin/tokenMapping.html.twig", [
                'apiKey' => $apiKey,
                'tokens' => $this->adminService->getTokenMapping(),
            ]
        );
    }

    /**
     * Check route integrity.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/checkRouteIntegrity", name="admin_check_route_integrity", methods={"GET", "POST"})
     */
    public function checkRouteIntegrity(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        return $this->render(
            "admin/routeIntegrity.html.twig", [
                'apiKey' => $apiKey,
                'routes' => $this->adminService->getRouteIntegrity(),
                'totalTokens' => $this->adminService->getTotalTokens(),
                'envServer' => strtoupper($_ENV['APP_ENV']),
                'tokensDiff' => $this->adminService->compareOnlineTokensData()
            ]
        );
    }

    /**
     * Drop tokens.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/dropTokens", name="admin_drop_tokens", methods={"GET", "POST"})
     */
    public function dropTokens(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

        if ($request->get('method') === 'delete') {
            $this->adminService->dropTokens();
        }

        return $this->render(
            "admin/dropTokens.html.twig", [
                'apiKey' => $apiKey
            ]
        );
    }

    /**
     * Manage token list.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/manageTokenList", name="admin_manage_token_list", methods={"GET", "POST"})
     */
    public function manageTokenList(Request $request): Response
    {
        // Check admin rights
        $apiKey = $this->getApiToken($request);
        $this->authenticatorService->checkAdminRights(
            $apiKey,
            $this->getRequestOrigin($request)
        );

//        // Form Chain
//        $tokenlistNetwork = new TokenlistNetwork();
//        $formNetwork = $this->createForm(TokenlistNetworkType::class, $tokenlistNetwork);
//        $formNetwork->handleRequest($request);
//
//        // Form Refer
//        $tokenlistRefer = new TokenlistRefer();
//        $formRefer = $this->createForm(TokenlistReferType::class, $tokenlistRefer);
//        $formRefer->handleRequest($request);
//
//        // Form Tag
//        $tokenlistTag = new TokenlistTag();
//        $formTag = $this->createForm(TokenlistTagType::class, $tokenlistTag);
//        $formTag->handleRequest($request);
//
//        // Form Token
//        $tokenlistToken = new TokenlistToken();
//        $formToken = $this->createForm(TokenlistTokenType::class, $tokenlistToken);
//        $formToken->handleRequest($request);

        switch ($request->get('method')) {
            case 'create':
                $this->adminService->createTokenList($request, $request->get('type'));
                break;
            case 'update':
                $this->adminService->updateTokenList($request, $request->get('type'));
                break;
            case 'delete':
                $this->adminService->deleteTokenList($request, $request->get('type'));
                break;
        }

        return $this->render(
            "admin/tokenList.html.twig", [
                'apiKey' => $apiKey,
//                'formNetwork' => $formNetwork->createView(),
//                'formRefer' => $formRefer->createView(),
//                'formTag' => $formTag->createView(),
//                'formToken' => $formToken->createView(),
                'tokenList' => $this->adminService->getTokenList(),
            ]
        );
    }

    /**
     * Get environments differences.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/environmentsDifferences", name="admin_environments_differences", methods={"GET"})
     */
    public function getEnvironmentsDifferences(Request $request): Response
    {
        // Check admin rights
        $this->authenticatorService->checkAdminRights(
            $this->getApiToken($request),
            $this->getRequestOrigin($request)
        );

        return new JsonResponse($this->adminService->compareOnlineTokensData(), Response::HTTP_OK);
    }
}
