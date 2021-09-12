<?php

namespace App\Service;

use App\Entity\Token;
use App\Entity\TokenlistIntegrity;
use App\Entity\TokenlistNetwork;
use App\Entity\TokenlistRefer;
use App\Entity\TokenlistTag;
use App\Traits\NetworkControllerTrait;
use DateTime;
use DOMDocument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefiService
 * @package App\Service
 */
class DefiService extends Service
{
    use NetworkControllerTrait;
    
    const URI_THEGRAPH = "https://api.thegraph.com/subgraphs/name";

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
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://realt.ch/tokensListes/?referer=" . $refer,
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

    /**
     * Parse TheGraph API.
     *
     * @param string $uri
     * @param string $query
     * @return array
     */
    public function theGraphApi(string $uri, string $query): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_URL => $uri,
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Get symbol from Etherscan.
     *
     * @param string $ethereumContract
     * @return false|mixed
     */
    public function getEtherscanSymbol(string $ethereumContract)
    {
        $uri = "https://etherscan.io/token/".$ethereumContract;
        $response = $this->curlRequest($uri);

        $doc = new DOMDocument();
        @$doc->loadHTML($response);

        $title = $doc->getElementsByTagName('title');
        $title = $title->item(0)->textContent;

        if ($title === "Etherscan Error Page") {
            return false;
        }

        preg_match("/\((.*)\)/", $title, $symbol);

        $name = null;
        if (!empty($symbol[1])) {
            $name = $symbol[1];
        }

        $validSymbol = strpos($name, "REALTOKEN-");

        if (!$name || $validSymbol !== 0) {
            return false;
        }

        return $name;
    }

    /**
     * Get symbol token from EtherscanDOM.
     *
     */
    public function generateTokenSymbol(): JsonResponse
    {
        $count = 0;
        $tokens = $this->em->getRepository(Token::class)->findAll();

        /** @var Token $token */
        foreach ($tokens as $token) {
            if (empty($token->getSymbol())) {
                if ($name = $this->getEtherscanSymbol($token->getEthereumContract())) {
                    $token->setSymbol($name);
                    $this->em->persist($token);
                    $count++;
                }
            }
        }

        $this->em->flush();

        return new JsonResponse($count." tokens symbol created.", Response::HTTP_OK);
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
                            "chainId" => $secondaryMarketplace["chainId"],
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

        // TODO : Make Service to get TheGraph data from SecondaryMarketplaces->Object !!
//        $this->getLpPair();

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
     * Get Lp Pair from TheGraph.
     *
     * @param $poolContract
     */
    private function getLpPair($poolContract)
    {
        $poolContract = "0x83a7c8b6b3824ac02cf79d8219d1bc779e8086d7"; // TODO: REMOVE !!!!!
        $uri = self::URI_THEGRAPH . '/levinswap/uniswap-v2'; // TODO : ADD DATA ON REFER COLUMN !!!
        $query = '{"query":"{pairs(where: {id:\"'.$poolContract.'\"}) {id token0 {id symbol name} token1 {id symbol name}}}"}';

        $data = $this->theGraphApi($uri, $query);

        // TODO : Parse JSON + check tokenContact and get other*
        // ...
    }
}
