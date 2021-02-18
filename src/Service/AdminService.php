<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Quota;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

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
     */
    public function getTotalUsersQuota()
    {
        $authenticator = new AuthenticatorService($this->request, $this->entityManager);
        $application = $authenticator->getApplicationByToken($this->request->query->get('realtAuthToken'));

        if (!empty($application)) {
            $isAdmin = $authenticator->applicationHaveAdminRights($application);
        }

        if (!($application Instanceof Application) || !$isAdmin) {
            return [];
        }

        $applicationRepository = $this->entityManager->getRepository(Application::class);
        $applicationsQuota = $applicationRepository->findAllWithQuota();

        return $this->doAppQuotaMapping($applicationsQuota);
    }

    /**
     * Mapping quota for applications.
     *
     * @param array $applicationsQuota
     *
     * @return array
     */
    private function doAppQuotaMapping(array $applicationsQuota): array
    {
        $result = [];
        foreach ($applicationsQuota as $application) {
            /** @var Quota $quota */
            $quota = $application->getQuota();
            /** @var User $user */
            $user = $application->getUser();

            if (!empty($quota)) {
                $quotaId = $quota->getId();
                $increment = $quota->getIncrement();
            } else {
                $quotaId = null;
                $increment = 0;
            }

            array_push($result, [
                'application' => [
                    'id' => $application->getId(),
                    'name' => $application->getName(),
                    'token' => $application->getApiToken(),
                ],
                'quota' => [
                    'id' => $quotaId,
                    'increment' => $increment,
                ],
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                    'ethereumAddress' => $user->getEthereumAddress(),
                ],
            ]);
        }

        return $result;
    }
}
