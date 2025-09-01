<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Web3p\EthereumUtil\Util;

class Web3Authenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserProviderInterface $userProvider,
        private Util $ethereumUtil
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'web3_login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $address = $request->request->get('address');
        $signature = $request->request->get('signature');
        $nonce = $request->request->get('nonce');

        if (!$address || !$signature || !$nonce) {
            throw new AuthenticationException('Missing parameters.');
        }

        // Récupérer le nonce attendu (depuis cache ou DB)
        $expectedNonce = $this->getNonceForAddress($address);
        if ($nonce !== $expectedNonce) {
            throw new AuthenticationException('Invalid nonce.');
        }

        // Vérifier la signature
        if (!$this->verifySignature($address, $nonce, $signature)) {
            throw new AuthenticationException('Invalid signature.');
        }

        return new SelfValidatingPassport(
            new UserBadge($address, function () use ($address) {
                return $this->userProvider->loadUserByUsername($address);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        // Retourner un token JWT ou réponse personnalisée
        return new JsonResponse(['message' => 'Authentication successful']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessageKey()], 401);
    }

    private function verifySignature(string $address, string $message, string $signature): bool
    {
        $msg = "\x19Ethereum Signed Message:\n" . strlen($message) . $message;
        $msgHash = $this->ethereumUtil->sha3($msg);

        $recoveredAddress = $this->ethereumUtil->recoverAddress($msgHash, $signature);

        return strtolower($recoveredAddress) === strtolower($address);
    }

    private function getNonceForAddress(string $address): string
    {
        // TODO: implémenter la récupération du nonce lié à l'adresse (cache/DB)
        return 'exemple-nonce-unique';
    }
}
