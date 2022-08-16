<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Quota;
use App\Entity\QuotaConfiguration;
use App\Entity\QuotaLimitations;
use App\Entity\Token;
use App\Entity\TokenlistIntegrity;
use App\Entity\TokenlistNetwork;
use App\Entity\TokenlistRefer;
use App\Entity\TokenlistTag;
use App\Entity\TokenlistToken;
use App\Entity\TokenMapping;
use App\Entity\User;
use App\Traits\NetworkControllerTrait;
use DateTime;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use TreeWalker;

/**
 * Class AdminService
 * @package App\Service
 */
class AdminService extends Service
{
    use NetworkControllerTrait;

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
     * V1
     */

    /**
     * Get quota limitations.
     *
     * @return array
     */
    public function getQuotaLimitations(): array
    {
        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
        return $quotaLimitationsRepository->findAll();
    }

    /**
     * Create quota limitations.
     *
     * @param Request $request
     */
    public function createQuotaLimitations(Request $request)
    {
        $quotaLimitation = new QuotaLimitations();
        $quotaLimitation->setRole($request->get('role'));
        $quotaLimitation->setLimitPerMinute($request->get('limitPerMinute'));
        $quotaLimitation->setLimitPerHour($request->get('limitPerHour'));
        $quotaLimitation->setLimitPerDay($request->get('limitPerDay'));
        $quotaLimitation->setLimitPerWeek($request->get('limitPerWeek'));
        $quotaLimitation->setLimitPerMonth($request->get('limitPerMonth'));
        $quotaLimitation->setLimitPerYear($request->get('limitPerYear'));
        $this->em->persist($quotaLimitation);
        $this->em->flush();
    }

    /**
     * Update quota limitations.
     *
     * @param Request $request
     */
    public function updateQuotaLimitations(Request $request)
    {
        $id = $request->get('id');
        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
        $quotaLimitation = $quotaLimitationsRepository->findOneBy(['id' => $id]);
        $quotaLimitation->setRole($request->get('role'));
        $quotaLimitation->setLimitPerMinute($request->get('limitPerMinute'));
        $quotaLimitation->setLimitPerHour($request->get('limitPerHour'));
        $quotaLimitation->setLimitPerDay($request->get('limitPerDay'));
        $quotaLimitation->setLimitPerWeek($request->get('limitPerWeek'));
        $quotaLimitation->setLimitPerMonth($request->get('limitPerMonth'));
        $quotaLimitation->setLimitPerYear($request->get('limitPerYear'));
        $this->em->persist($quotaLimitation);
        $this->em->flush();
    }

    /**
     * Delete quota limitations.
     *
     * @param Request $request
     */
    public function deleteQuotaLimitations(Request $request)
    {
        $id = $request->get('id');
        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
        $quotaLimitation = $quotaLimitationsRepository->findOneBy(['id' => $id]);

        $this->em->remove($quotaLimitation);
        $this->em->flush();
    }

    /**
     * V2
     */

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
            '/v1/token',
            '/v1/token/0xe5f7ef61443fc36ae040650aa585b0395aef77c8',
            '/v1/token/lastUpdate',
            '/v1/quota',
        ];

        foreach ($urls as $url) {
            $uri = $_SERVER["SERVER_NAME"] . $url;
            $response = $this->curlRequest($uri, true);

            if (!isset($response['status'])) {
                $response = substr(json_encode($response),0,85);
            } else {
                $response = ["error" => ["code" => 401, "message" => $response["message"]]];
            }

            $routes[] = [
                "url" => substr($url, 0, 20),
                "response" => $response,
            ];
        }

        return $routes;
    }

    public function getTotalTokens()
    {
        $tokenRepository = $this->em->getRepository(Token::class);
        return $tokenRepository->countAllTokens();
    }

    /**
     * Drop all tokens.
     *
     */
    public function dropTokens()
    {
        $tokenRepository = $this->em->getRepository(Token::class);
        $tokenRepository->dropTokens();
    }

    /**
     * Get token list.
     *
     * @return array
     */
    public function getTokenList(): array
    {
        $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
        $tokenListNetwork = $tokenListNetworkRepository->findAll();

        $tokenListIntegrityTypes = $this->em->getRepository(TokenlistIntegrity::class);
        $tokenListTypes = $tokenListIntegrityTypes->findAll();

        $tokenListReferRepository = $this->em->getRepository(TokenlistRefer::class);
        $tokenListRefer = $tokenListReferRepository->findAll();

        $tokenListTagRepository = $this->em->getRepository(TokenlistTag::class);
        $tokenListTag = $tokenListTagRepository->findAll();

        $tokenListTokenRepository = $this->em->getRepository(TokenlistToken::class);
        $tokenListToken = $tokenListTokenRepository->findAll();

        return [
            'chains' => $tokenListNetwork,
            'types' => $tokenListTypes,
            'refers' => $tokenListRefer,
            'tags' => $tokenListTag,
            'tokens' => $tokenListToken,
        ];
    }

    /**
     * Create token list.
     *
     * @param Request $request
     * @param string $type
     */
    public function createTokenList(Request $request, string $type)
    {
        switch ($type) {
            case 'chain':
                $tokenListNetwork = new TokenlistNetwork();
                $tokenListNetwork->setChainId($request->get('chainId'));
                $tokenListNetwork->setName($request->get('name'));
                $this->em->persist($tokenListNetwork);
                break;
            case 'type':
                $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
                /** @var TokenlistNetwork $chain */
                $chain = $tokenListNetworkRepository->findOneBy(['id' => $request->get('chain')]);

                $tokenListIntegrity = new TokenlistIntegrity();
                $tokenListIntegrity->setTimestamp(new DateTime('1970-01-01'));
                $tokenListIntegrity->setNetwork($chain);
                $tokenListIntegrity->setVersionMajor($request->get('major'));
                $tokenListIntegrity->setVersionMinor($request->get('minor'));
                $tokenListIntegrity->setVersionPatch($request->get('patch'));
                $tokenListIntegrity->setHash('');
                $tokenListIntegrity->setData([]);
                $this->em->persist($tokenListIntegrity);
                break;
            case 'refer':
                $tokenListIntegrityRepository = $this->em->getRepository(TokenlistIntegrity::class);
                /** @var TokenlistIntegrity $types */
                $types = $tokenListIntegrityRepository->findOneBy(['id' => $request->get('types')]);

                $tokenListRefer = new TokenlistRefer();
                $tokenListRefer->setName($request->get('name'));
                $tokenListRefer->setUrl($request->get('url'));
                $tokenListRefer->setIntegrityTypes($types);
                $this->em->persist($tokenListRefer);
                break;
            case 'tag':
                $tokenListTag = new TokenlistTag();
                $tokenListTag->setTagKey($request->get('tagKey'));
                $tokenListTag->setName($request->get('name'));
                $tokenListTag->setDescription($request->get('description'));
                $this->em->persist($tokenListTag);
                break;
            case 'token':
                $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
                /** @var TokenlistNetwork $chain */
                $chain = $tokenListNetworkRepository->findOneBy(['id' => $request->get('chain')]);

                $tokenListTagRepository = $this->em->getRepository(TokenlistTag::class);
                $tags = $tokenListTagRepository->findBy(['id' => $request->get('tags')]);

                $tokenListToken = new TokenlistToken();
                $tokenListToken->setAddress($request->get('address'));
                $tokenListToken->setChain($chain);
                $tokenListToken->setName($request->get('name'));
                $tokenListToken->setSymbol($request->get('symbol'));
                $tokenListToken->setDecimals($request->get('decimals'));
                $tokenListToken->setTags($tags);
                $this->em->persist($tokenListToken);
                break;
        }
        $this->em->flush();
    }

    /**
     * Update token list.
     *
     * @param Request $request
     * @param string $type
     */
    public function updateTokenList(Request $request, string $type)
    {
        $id = $request->get('id');

        switch ($type) {
            case 'chain':
                $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
                $tokenList = $tokenListNetworkRepository->findOneBy(['id' => $id]);
                $tokenList->setChainId($request->get('chainId'));
                $tokenList->setName($request->get('name'));
                $this->em->persist($tokenList);
                break;
            case 'type':
                $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
                /** @var TokenlistNetwork $chain */
                $chain = $tokenListNetworkRepository->findOneBy(['id' => $request->get('chain')]);

                $tokenListIntegrityRepository = $this->em->getRepository(TokenlistIntegrity::class);
                $tokenList = $tokenListIntegrityRepository->findOneBy(['id' => $id]);
                $tokenList->setNetwork($chain);
                $tokenList->setVersionMajor($request->get('major'));
                $tokenList->setVersionMinor($request->get('minor'));
                $tokenList->setVersionPatch($request->get('patch'));
                $tokenList->setHash($request->get('hash'));
                $this->em->persist($tokenList);
                break;
            case 'refer':
                $tokenListIntegrityRepository = $this->em->getRepository(TokenlistIntegrity::class);
                $types = $tokenListIntegrityRepository->findOneBy(['id' => $request->get('types')]);

                // TODO : Fix unique type response
//                var_dump($request->get('types'));
//                echo('<br>');
//                var_dump($types);die();

                $tokenListReferRepository = $this->em->getRepository(TokenlistRefer::class);
                $tokenList = $tokenListReferRepository->findOneBy(['id' => $id]);
                $tokenList->setName($request->get('name'));
                $tokenList->setUrl($request->get('url'));
                $tokenList->setIntegrityTypes($types);
                $this->em->persist($tokenList);
                break;
            case 'tag':
                $tokenListTagRepository = $this->em->getRepository(TokenlistTag::class);
                $tokenList = $tokenListTagRepository->findOneBy(['id' => $id]);
                $tokenList->setTagKey($request->get('tagKey'));
                $tokenList->setName($request->get('name'));
                $tokenList->setDescription($request->get('description'));
                $this->em->persist($tokenList);
                break;
            case 'token':
                $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
                /** @var TokenlistNetwork $chain */
                $chain = $tokenListNetworkRepository->findOneBy(['id' => $request->get('chain')]);

                $tokenListTagRepository = $this->em->getRepository(TokenlistTag::class);
                $tags = $tokenListTagRepository->findAllWithIds([$request->get('tags')]);

                // TODO : Fix unique tag response
//                var_dump($request->get('tags'));
//                echo('<br>');
//                var_dump($tags);die();

                $tokenListTokenRepository = $this->em->getRepository(TokenlistToken::class);
                $tokenList = $tokenListTokenRepository->findOneBy(['id' => $id]);
                $tokenList->setAddress($request->get('address'));
                $tokenList->setChain($chain);
                $tokenList->setName($request->get('name'));
                $tokenList->setSymbol($request->get('symbol'));
                $tokenList->setDecimals($request->get('decimals'));
                $tokenList->setTags($tags);
                $this->em->persist($tokenList);
                break;
        }
        $this->em->flush();
    }

    /**
     * Delete token list.
     *
     * @param Request $request
     * @param string $type
     */
    public function deleteTokenList(Request $request, string $type)
    {
        $id = $request->get('id');

        switch ($type) {
            case 'chain':
                $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
                $tokenList = $tokenListNetworkRepository->findOneBy(['id' => $id]);
                break;
            case 'type':
                $tokenListIntegrityRepository = $this->em->getRepository(TokenListIntegrity::class);
                $tokenList = $tokenListIntegrityRepository->findOneBy(['id' => $id]);
                break;
            case 'refer':
                $tokenListReferRepository = $this->em->getRepository(TokenlistRefer::class);
                $tokenList = $tokenListReferRepository->findOneBy(['id' => $id]);
                break;
            case 'tag':
                $tokenListTagRepository = $this->em->getRepository(TokenlistTag::class);
                $tokenList = $tokenListTagRepository->findOneBy(['id' => $id]);
                break;
            case 'token':
                $tokenListTokenRepository = $this->em->getRepository(TokenlistToken::class);
                $tokenList = $tokenListTokenRepository->findOneBy(['id' => $id]);
                break;
        }

        $this->em->remove($tokenList);
        $this->em->flush();
    }

    /**
     * Compare online tokens data.
     *
     * @return stdClass
     */
    public function compareOnlineTokensData(): stdClass
    {
        $ENDPOINT_PREPROD = "https://api.preprod.realt.community/v1/token";
        $ENDPOINT_PROD = "https://api.realt.community/v1/token";

        $headers = array();
        $headers[] = 'Accept: */*';
        $headers[] = 'X-Auth-Realt-Token: ' . $_ENV["API_TOKEN_CHECK_HEALTH"];

        $onlineDataPreprod = $this->curlRequest($ENDPOINT_PREPROD, false, $headers);
        $onlineDataProd = $this->curlRequest($ENDPOINT_PROD, false, $headers);

        $onlineDataPreprod = $this->formatDataForParsing($onlineDataPreprod);
        $onlineDataProd = $this->formatDataForParsing($onlineDataProd);

        $treeWalker = new TreeWalker([]);

        // Get json diff
        if ($_ENV['APP_ENV'] === "prod") {
            $filteredResult = $treeWalker->getdiff($onlineDataProd, $onlineDataPreprod, true);
        } else {
            $filteredResult = $treeWalker->getdiff($onlineDataPreprod, $onlineDataProd, true);
        }

        // Remove values
        $treeWalker->walker($filteredResult, function(&$struct, $key) {
            if ($key == "lastUpdate") {
                unset($struct[$key]);
            }
        });

        // Check empty
        foreach ($filteredResult as $key => $options) {
            foreach ($options as $id => $values) {
                if (empty($values)) {
                    unset($filteredResult[$key][$id]);
                }
            }
        }

        return json_decode(json_encode($filteredResult));
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
                    'referer' => $application->getReferer(),
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
     * Format data for parsing.
     *
     * @param $jsonData
     *
     * @return string
     */
    private function formatDataForParsing($jsonData): string
    {
        $orderedArray = [];

        $array = json_decode($jsonData);
        foreach($array as $value) {
            $orderedArray[$value->uuid] = $value;
        }

        return json_encode($orderedArray);
    }
}
