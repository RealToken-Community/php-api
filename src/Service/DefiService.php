<?php

namespace App\Service;

use App\Entity\Token;
use App\Entity\TokenlistIntegrity;
use App\Entity\TokenlistNetwork;
use App\Entity\TokenlistRefer;
use App\Entity\TokenlistTag;
use App\Traits\NetworkControllerTrait;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class DefiService
 * @package App\Service
 */
class DefiService extends Service
{
    use NetworkControllerTrait;

    const URI_THEGRAPH = "https://api.thegraph.com/subgraphs/name";

    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheInterface $cache)
    {
        $this->cache = $cache;

        parent::__construct($entityManager);
    }

    /**
     * Generate token list for AMM.
     *
     * @param string|null $refer
     *
     * @return JsonResponse
     */
    public function getTokenListForAMMDeprecated(?string $refer): JsonResponse
    {
        $ammList = $this->getCommunityList($refer);

        // TODO : future list
//        $ammList = $this->tokenListMapping();

        return new JsonResponse($ammList, Response::HTTP_OK);
    }

    /**
     * Generate token list for AMM (beta).
     *
     * @param string|null $refer
     *
     * @return JsonResponse
     */
    public function getTokenListForAMM(?string $refer): JsonResponse
    {
        $ammList = $this->createCommunityList($refer);

        return new JsonResponse($ammList, Response::HTTP_OK);
    }

    /**
     * Create AMM community list.
     *
     *
     */
    public function createCommunityList(?string $refer): array
    {
        return $this->tokenListMapping($refer);
    }

    /**
     * Get AMM community list DOM.
     *
     * @param string|null $refer
     *
     * @return array
     */
    public function getCommunityList(?string $refer): array
    {
        return $this->curlRequest("https://realt.ch/tokensListes/?referer=" . $refer, true);
    }

//    /**
//     * Parse TheGraph API.
//     *
//     * @param string $uri
//     * @param string $query
//     * @return array
//     */
//    public function theGraphApi(string $uri, string $query): array
//    {
//        $ch = curl_init();
//        curl_setopt_array($ch, array(
//            CURLOPT_HEADER => false,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_SSL_VERIFYPEER => false,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => $query,
//            CURLOPT_URL => $uri,
//        ));
//
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//        return json_decode($response);
//    }

    /**
     * Get symbol from Etherscan.
     *
     * @param string $ethereumContract
     *
     * @return false|mixed
     * @throws InvalidArgumentException
     */
    public function getEtherscanSymbol(string $ethereumContract)
    {
        return $this->cache->get($ethereumContract.'-symbol', function (ItemInterface $item, bool &$save) use ($ethereumContract) {
            // no expire so we can always use cached data
            // if cache needs to be flushed just go to the database and TRUNCATE the cache table
            $save = true;

            $uri = "https://etherscan.io/token/".$ethereumContract;
            $response = $this->curlRequest($uri);

            $doc = new DOMDocument();
            @$doc->loadHTML($response);

            $title = $doc->getElementsByTagName('title');
            $title = $title->item(0)->textContent;

            if ($title === "Etherscan Error Page") {
                $save = false;
            }

            preg_match("/\((.*)\)/", $title, $symbol);

            $name = null;
            if (!empty($symbol[1])) {
                $name = $symbol[1];
            }

            $validSymbol = strpos($name, "REALTOKEN-");

            if (!$name || $validSymbol !== 0) {
                $save = false;
            }

            return $name;
        });
    }

    /**
     * Get symbol token from EtherscanDOM.
     *
     * @return JsonResponse
     */
    public function generateTokenSymbol(): JsonResponse
    {
        $count = $emptySymbol = 0;
        $tokens = $this->em->getRepository(Token::class)->findAll();

        /** @var Token $token */
        foreach ($tokens as $token) {
            if (empty($token->getSymbol())) {
                ++$emptySymbol;
                if ($name = $this->getEtherscanSymbol($token->getEthereumContract())) {
                    $token->setSymbol($name);
                    $this->em->persist($token);
                    ++$count;
                }
            }
        }

        $this->em->flush();

        return new JsonResponse([
            'updated_tokens_with_symbol' => $count,
            'tokens_to_update' => $emptySymbol - $count,
        ], $count !== $emptySymbol ? Response::HTTP_MULTI_STATUS : Response::HTTP_OK);
    }

    /**
     * Generate LP pair token.
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function generateLpPairToken(): JsonResponse
    {
        $count = $emptyLpPair = 0;
        $tokens = $this->em->getRepository(Token::class)->findAll();

        /** @var Token $token */
        foreach ($tokens as $token) {
            // Check if secondaryMarketplaces is different
            if ($this->checkMarketplacesDifference($token)) {
                // Get xDai Contract
                $secondaryMarketplaces = $token->getOriginSecondaryMarketplaces();

                foreach ($secondaryMarketplaces as $key => $secondaryMarketplace) {
                    if (array_key_exists("pair", $secondaryMarketplace)) {
                        continue;
                    }
                    $chainName = strtolower($secondaryMarketplace["chainName"]);

                    // Tmp xDaiChain fix
                    // TODO : Add enum and chainId behind chainName
                    if ((
                        $chainName === "xdaichain" ||
                        $chainName === "ethereum"
                    ) && $token->getSymbol()) {
                        ++$emptyLpPair;
                        $pairToken = $this->getLpPairToken($chainName, $secondaryMarketplace["contractPool"], $token->getSymbol());
                        if (!empty($pairToken)) {
                            $secondaryMarketplaces[$key]["pair"] = $pairToken;
                            $token->setSecondaryMarketplaces($secondaryMarketplaces);
                            ++$count;
                        }
                    }
                }
            }
        }

        $this->em->flush();

        return new JsonResponse([
            'updated_tokens_with_lp_pair' => $count,
            'tokens_to_update' => $emptyLpPair - $count,
        ], $count !== $emptyLpPair ? Response::HTTP_MULTI_STATUS : Response::HTTP_OK);
    }

    /**
     * Token list mapping.
     *
     * @param string|null $refer
     *
     * @return array
     */
    private function tokenListMapping(?string $refer): array
    {
        $response = [];
        $integrityType = null;

        $tokenRepository = $this->em->getRepository(Token::class);
        $tokens = $tokenRepository->findAll();

        $tokenListReferRepository = $this->em->getRepository(TokenlistRefer::class);
        /** @var TokenlistRefer $tokenListRefer */
        $tokenListRefer = $tokenListReferRepository->findOneBy(["url" => $refer]);

        if (!empty($tokenListRefer)) {
            /** @var TokenlistIntegrity $integrityType */
            $integrityType = $tokenListRefer->getIntegrityTypes();
        }

        if (!empty($integrityType)) {
            $hashIntegrity = $integrityType->getHash();
            $hashToken = md5(serialize($tokens));

            if (!hash_equals($hashToken, $hashIntegrity)) {
                $version = $this->checkAndUpdateTokenListVersion($integrityType);

                $data = $this->generateTokenList($tokens, $version, $integrityType->getNetwork());
                array_push($response, $data);
                $integrityType->setTimestamp(new DateTime());
                $integrityType->setHash($hashToken);
                $integrityType->setData($data);
                $this->em->persist($integrityType);
            } else {
                array_push($response, $integrityType->getData());
            }
        } else {
            $tokenListNetworkRepository = $this->em->getRepository(TokenlistNetwork::class);
            /** @var TokenlistNetwork $tokenListNetwork */
            $tokenListNetwork = $tokenListNetworkRepository->findOneBy(['chainId' => 0]);

            $integrityTypeRepository = $this->em->getRepository(TokenlistIntegrity::class);
            /** @var TokenlistIntegrity $integrityType */
            $integrityType = $integrityTypeRepository->findOneBy(["network" => $tokenListNetwork]);

            $hashIntegrity = $integrityType->getHash();
            $hashToken = md5(serialize($tokens));

            if (!hash_equals($hashToken, $hashIntegrity)) {
                $version = $this->checkAndUpdateTokenListVersion($integrityType);

                $response[0] = $this->generateTokenList($tokens, $version, $integrityType->getNetwork());
                $integrityType->setTimestamp(new DateTime());
                $integrityType->setHash($hashToken);
                $integrityType->setData($response[0]);
                $this->em->persist($integrityType);
            } else {
                $response[0] = $integrityType->getData();
            }
        }

        $this->em->flush();
        return $response[0];
    }

    /**
     * Generate TokenList.
     *
     * @param array $tokens
     * @param array $version
     * @param TokenlistNetwork $network
     * @return array
     */
    private function generateTokenList(array $tokens, array $version, TokenlistNetwork $network): array
    {
        $tagsList = $tokenListTokens['tokens'] = [];

        $tokenListTagRepository = $this->em->getRepository(TokenlistTag::class);
        $tokenListTags = $tokenListTagRepository->findAllArrayResponse();

        $dateTime = new DateTime();

        $keywords = [
            0 => "Uniswap",
            1 => "DeFi",
            2 => "RealT",
            3 => "Ethereum",
            4 => "xDai chain",
        ];

        foreach ($tokenListTags as $tokenListTag) {
            $tagsList[$tokenListTag["tagKey"]] = ["name" => $tokenListTag["name"], "description" => $tokenListTag["description"]];
        }

        if ($_ENV['APP_ENV'] === "prod") {
            $name = "RealToken";
        } else {
            $name = "RealToken Dev";
        }

        $data = [
            "name"      =>	$name,
            "logoURI"   =>	"https://realt.co/wp-content/uploads/2019/01/cropped-RealToken_Favicon.png",
            "timestamp" =>	$dateTime->format("Y-m-d\TH:i:sP"),
            "version"   => [
                "major" => $version["major"],
                "minor" => $version["minor"],
                "patch" => $version["patch"]
            ],
            "keywords" => $keywords,
            // REGEX to have autorized char : https://github.com/Uniswap/token-lists#authoring-token-lists
            "tags" => $tagsList
        ];

        // TODO : Tmp remove Stablecoins - Integration soon
//        $tokenListTokenRepository = $this->em->getRepository(TokenlistToken::class);
//        $tokenListTokens['tokens'] = $tokenListTokenRepository->findAllArrayResponse();

        /** @var Token $token */
        foreach ($tokens as $token) {
            $secondaryMarketplaces = $token->getSecondaryMarketplaces();
            $blockchainsAddresses = $token->getBlockchainAddresses();

            foreach ($secondaryMarketplaces as $secondaryMarketplace) {
                $tags = [];

                $chainName = strtolower($secondaryMarketplace["chainName"]);
                $dexName = strtolower($secondaryMarketplace["dexName"]);

                // Tmp xDaiChain fix
                if (strtolower($chainName) === "xdaichain") {
                    $chainName = "xDai";
                }

                if (isset($blockchainsAddresses[$chainName])
                    || strtolower($network->getName()) === "all"
                ) {
                    if (!empty($blockchainsAddresses[$chainName]["contract"])
                        || $network->getChainId() === 0
                    ) {
                        // Add tag from secondaryMarketplaces pair
                        if (isset($secondaryMarketplace["pair"])) {
                            $pairSymbol = strtolower($secondaryMarketplace["pair"]["symbol"]);
                            foreach ($tokenListTags as $tokenListTag) {
                                if (strpos(strtolower($tokenListTag["tagKey"]), $pairSymbol) !== false
                                    && strpos(strtolower($tokenListTag["tagKey"]), "pair") !== false) {
                                    array_push($tags, $tokenListTag["tagKey"]);
                                }
                            }
                        }

                        // Add dex tag
                        foreach ($tokenListTags as $tokenListTag) {
                            if ($dexName === strtolower($tokenListTag["name"])) {
                                array_push($tags, $tokenListTag["tagKey"]);
                            }
                        }

                        // Regex to format long shortname
                        $shortname = $token->getShortName();
                        if (strlen($shortname) > 20) {
                            $pattern = "/((.*-)(.*[0-9])-(.*[0-9]) (.*))|((.*[0-9])-(.*[0-9]) (.*))/";
                            preg_match($pattern, $shortname, $matches);
                            if (!empty($matches[1])) {
                                $shortname = $matches[2] . $matches[3] . " " . $matches[5];
                            } else {
                                $shortname = $matches[7] . " " . $matches[9];
                            }
                        }

                        $tokenData = [
                            "address" => $blockchainsAddresses[$chainName]["contract"],
                            "chainId" => (int)$secondaryMarketplace["chainId"],
                            "name" => $shortname,
                            "symbol" => strtoupper(str_replace(" ", "-", str_replace(".", "", $shortname))),
                            "decimals" => 18,
                            "logoURI" => "https://realt.co/wp-content/uploads/2019/01/cropped-RealToken_Favicon.png",
                            "tags" => $tags,
                        ];
                        array_push($tokenListTokens['tokens'], $tokenData);
                    }
                }
            }
        }

        $data['tokens'] = $tokenListTokens['tokens'];

        return $data;
    }

    /**
     * Check and update token list version.
     *
     * @param TokenlistIntegrity $integrityType
     * @return array
     */
    private function checkAndUpdateTokenListVersion(TokenlistIntegrity $integrityType): array
    {
        // Autoincrement minor version
        $integrityType->setVersionMajor($integrityType->getVersionMajor());
        $integrityType->setVersionMinor($integrityType->getVersionMinor() + 1);
        $integrityType->setVersionPatch($integrityType->getVersionPatch());

        return [
            "major" => $integrityType->getVersionMajor(),
            "minor" => $integrityType->getVersionMinor(),
            "patch" => $integrityType->getVersionPatch()
        ];
    }

    /**
     * Check difference from Json secondaryMarketplaces and token data.
     *
     * @param $token
     *
     * @return bool
     */
    private function checkMarketplacesDifference($token): bool
    {
        $hashOrigin = md5(serialize($token->getOriginSecondaryMarketplaces()));
        $hashWithPair = md5(serialize($token->getSecondaryMarketplaces()));

        if (hash_equals($hashOrigin, $hashWithPair) && !empty($hashOrigin)) {
            return true;
        }

        return false;
    }

    /**
     * Get LP pair tokens from Blockscout.
     *
     * @param $network
     * @param $contractAddress
     * @param $tokenSymbol
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getLpPairToken($network, $contractAddress, $tokenSymbol): array
    {
        return $this->cache->get($network.'-'.$contractAddress.'-'.$tokenSymbol, function (ItemInterface $item, bool &$save) use ($network, $contractAddress, $tokenSymbol) {
            // no expire so we can always use cached data
            // if cache needs to be flushed just go to the database and TRUNCATE the cache table
            $save = true;

            if ($network === "ethereum") {
                $uri = "https://api.etherscan.io/api?module=account&action=tokentx&address=".$contractAddress."&sort=asc";
            } else {
                $uri = "https://blockscout.com/xdai/mainnet/api?module=account&action=tokentx&address=".$contractAddress."&sort=asc";
            }
            $json = $this->curlRequest($uri);

            $response = json_decode($json, true);

            // Ignore error & UniswapV1
            if (empty($response)
                || $response["status"] === "0"
                || $response["result"][0]["hash"] != $response["result"][1]["hash"]) {
                $save = false;
                return [];
            }

            if ($response["result"][0]["tokenSymbol"] !== $tokenSymbol) {
                $index = 0;
            } else {
                $index = 1;
            }

            $lpPair["contract"] = $response["result"][$index]["contractAddress"];
            $lpPair["symbol"] = $response["result"][$index]["tokenSymbol"];
            $lpPair["name"] = $response["result"][$index]["tokenName"];

            return $lpPair;
        });
    }
}
