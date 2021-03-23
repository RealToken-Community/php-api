<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Quota;
use App\Entity\QuotaConfiguration;
use App\Entity\TokenMapping;
use App\Entity\User;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminService
 * @package App\Service
 */
class AdminService extends Service
{
    /**
     * Get total users quota.
     *
     * @return array
     */
    public function getTotalUsersQuota(): array
    {
        $applicationRepository = $this->em->getRepository(Application::class);
        $applicationsQuota = $applicationRepository->findAllWithQuota();

        return $this->doAppQuotaMapping($applicationsQuota);
    }

    /**
     * Get quota management.
     *
     * @return array
     */
    public function getQuotaManagement(): array
    {
        $quotaConfigurationRepository = $this->em->getRepository(QuotaConfiguration::class);
        return $quotaConfigurationRepository->findAll();
    }

    /**
     * Create quota management.
     *
     * @param Request $request
     */
    public function createQuotaManagement(Request $request)
    {
        $quotaConfiguration = new QuotaConfiguration();
        $quotaConfiguration->setName($request->get('name'));
        $quotaConfiguration->setLimitation($request->get('limitation'));
        $quotaConfiguration->setIntervalNumber($request->get('intervalNumber'));
        $quotaConfiguration->setIntervalType($request->get('intervalType'));
        $this->em->persist($quotaConfiguration);
        $this->em->flush();
    }

    /**
     * Update quota management.
     *
     * @param Request $request
     */
    public function updateQuotaManagement(Request $request)
    {
        $id = $request->get('id');
        $quotaConfigurationRepository = $this->em->getRepository(QuotaConfiguration::class);
        $quotaConfiguration = $quotaConfigurationRepository->findOneBy(['id' => $id]);
        $quotaConfiguration->setName($request->get('name'));
        $quotaConfiguration->setLimitation($request->get('limitation'));
        $quotaConfiguration->setIntervalNumber($request->get('intervalNumber'));
        $quotaConfiguration->setIntervalType($request->get('intervalType'));
        $this->em->persist($quotaConfiguration);
        $this->em->flush();
    }

    /**
     * Delete quota management.
     *
     * @param Request $request
     */
    public function deleteQuotaManagement(Request $request)
    {
        $id = $request->get('id');
        $quotaConfigurationRepository = $this->em->getRepository(QuotaConfiguration::class);
        $quotaConfiguration = $quotaConfigurationRepository->findOneBy(['id' => $id]);

        $this->em->remove($quotaConfiguration);
        $this->em->flush();
    }

    /**
     * Get token mapping.
     *
     * @return array
     */
    public function getTokenMapping(): array
    {
        $tokenMappingRepository = $this->em->getRepository(TokenMapping::class);
        return $tokenMappingRepository->findAll();
    }

    /**
     * Create token mapping.
     *
     * @param Request $request
     */
    public function createTokenMapping(Request $request)
    {
        $tokenMapping = new TokenMapping();
        $tokenMapping->setSourceName($request->get('sourceName'));
        $tokenMapping->setDestinationName($request->get('destinationName'));
        $tokenMapping->setLastUpdate(new DateTime());
        $this->em->persist($tokenMapping);
        $this->em->flush();
    }

    /**
     * Update token mapping.
     *
     * @param Request $request
     */
    public function updateTokenMapping(Request $request)
    {
        $id = $request->get('id');
        $tokenMappingRepository = $this->em->getRepository(TokenMapping::class);
        $tokenMapping = $tokenMappingRepository->findOneBy(['id' => $id]);
        $tokenMapping->setSourceName($request->get('sourceName'));
        $tokenMapping->setDestinationName($request->get('destinationName'));
        $tokenMapping->setLastUpdate(new DateTime());
        $this->em->persist($tokenMapping);
        $this->em->flush();
    }

    /**
     * Delete token mapping.
     *
     * @param Request $request
     */
    public function deleteTokenMapping(Request $request)
    {
        $id = $request->get('id');
        $tokenMappingRepository = $this->em->getRepository(TokenMapping::class);
        $tokenMapping = $tokenMappingRepository->findOneBy(['id' => $id]);

        $this->em->remove($tokenMapping);
        $this->em->flush();
    }

    /**
     * Get route integrity.
     *
     * @return array
     */
    public function getRouteIntegrity():array
    {
        $routes = [];
        $urls = [
            '/v1/tokenList',
            '/v1/tokens',
            '/v1/token/0xe5f7ef61443fc36ae040650aa585b0395aef77c8',
        ];

        foreach ($urls as $url) {
            $response = $this->checkRoute($url);

            if (!isset($response['error'])) {
                $response = substr(json_encode($response),0,85);
            }

            array_push($routes, [
                "url" => substr($url, 0, 20),
                "response" => $response,
            ]);
        }

        return $routes;
    }

    /**
     * Check quota configuration existence.
     *
     * @param string $name
     *
     * @return QuotaConfiguration|null
     */
    private function checkQuotaConfigurationExistence(string $name): ?QuotaConfiguration
    {
        $quotaConfigurationRepository = $this->em->getRepository(QuotaConfiguration::class);
        $quotaConfiguration = $quotaConfigurationRepository->findOneBy(['name' => $name]);

        if (!$quotaConfiguration instanceof QuotaConfiguration) {
            return null;
        }

        return $quotaConfiguration;
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

    /**
     * @param string $url
     *
     * @return array
     */
    private function checkRoute(string $url): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $_SERVER["SERVER_NAME"] . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
