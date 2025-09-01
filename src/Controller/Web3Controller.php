<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Web3Controller
{
    #[Route('/web3/nonce', name: 'web3_nonce', methods: ['POST'])]
    public function getNonce(): JsonResponse
    {
        // Générer un nonce unique à stocker côté serveur (ex: Redis, DB)
        $nonce = bin2hex(random_bytes(16));

        // Stocker le nonce en base/caché pour l'adresse (à implémenter)

        return new JsonResponse(['nonce' => $nonce]);
    }

    #[Route('/web3/login', name: 'web3_login', methods: ['POST'])]
    public function login()
    {
        // Cette route sera interceptée par Web3Authenticator
        // Le contrôleur ne fait rien ici, toute la logique est dans l’authenticator
    }
}
