<?php

namespace App\Service;

use App\Entity\Application;
use App\Security\TokenAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthenticatorService
 * @package App\Service
 */
class AuthenticatorService
{
    private $em;
    protected $request;

    /**
     * AuthenticatorService constructor.
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     */
    public function __construct(Request $request, EntityManagerInterface $em)
    {
        $this->request = $request;
        $this->em = $em;
    }

    /**
     * Check if user is auth.
     *
     * @return array|JsonResponse
     */
    public function checkCredentials()
    {
        $response = new JsonResponse();

        $headerParam = $this->request->headers->get('X-AUTH-REALT-TOKEN');
        $queryParam = $this->request->query->get('realtAuthToken');

        $apiKey = null;
        if (!empty($headerParam)) {
            $apiKey = $headerParam;
        } elseif (!empty($queryParam)) {
            $apiKey = $queryParam;
        }

        $credentials = ['isAdmin' => false];

        if (!empty($apiKey)) {
            $application = $this->getApplicationByToken($apiKey);

            if (!($application Instanceof Application)) {
                $response->setData(["status" => "error", "message" => "Token is not recognized"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            $user = $application->getUser();
            $roles = $user->getRoles();

            if (in_array("ROLE_ADMIN", $roles)) {
                $credentials = ['isAdmin' => true];
            }

            $tokenAuthenticator = new TokenAuthenticator($this->em);
            $isAuth = $tokenAuthenticator->supports($this->request);

            if (!$isAuth) {
                $response->setData(["status" => "error", "message" => "Invalid API Token"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            $quotaService = new QuotaService($this->em);
            $quotaService->consumeQuota($application);

            $credentials['isAuth'] = true;
            return $credentials;
        }
        $credentials['isAuth'] = false;
        return $credentials;
    }

    /**
     * Get Application by token string.
     *
     * @param string $apiKey
     *
     * @return Application|null
     */
    public function getApplicationByToken(string $apiKey): ?Application
    {
        $applicationRepository = $this->em->getRepository(Application::class);
        return $applicationRepository->findOneBy(['apiToken' => $apiKey]);
    }

    /**
     * Check if Application have admin rights.
     *
     * @param Application $application
     *
     * @return bool
     */
    public function applicationHaveAdminRights(Application $application): bool{
        $user = $application->getUser();
        $roles = $user->getRoles();

        if (in_array("ROLE_ADMIN", $roles)) {
            return true;
        }

        return false;
    }
}