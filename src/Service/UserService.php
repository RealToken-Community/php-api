<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\User;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserService
 * @package App\Service
 */
class UserService extends Service
{
    /**
     * Register user form
     *
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    public function userRegistration(Request $request): array
    {
        if ($request->get('isAdmin')) {
            $roles = ["ROLE_USER", "ROLE_ADMIN"];
        } else {
            $roles = ["ROLE_USER"];
        }

        $user = $this->checkUserExistence($request->get('email'));
        
        if (!$user) {
            $user = new User();
            $user->setEmail($request->get('email'));
            $user->setRoles($roles);
            $user->setPassword($this->generatePassword());
            $user->setUsername($request->get('username'));
            $user->setEthereumAddress($request->get('ethereumAddress'));
            $this->em->persist($user);
        }

        $application = new Application();
        $application->setUser($user);
        $application->setName($request->get('appName'));
        $application->setApiToken($this->generateToken());

        $this->em->persist($application);
        $this->em->flush();

        return ['user' => $user, 'application' => $application];
    }

    /**
     * Generate unique token.
     *
     * @return string
     * @throws Exception
     */
    private function generateToken(): string
    {
        return $this->generateUuid(8).'-preprod-1'.$this->generateUuid(3).'-'.$this->generateUuid(4).'-'.$this->generateUuid(12);
    }

    /**
     * Generate unique uuid.
     *
     * @param int $length
     *
     * @return string
     * @throws Exception
     */
    private function generateUuid(int $length): string
    {
        return substr(bin2hex(random_bytes(15)), 0, $length);
    }

    /**
     * Generate random password.
     *
     * @param int $length
     *
     * @return string
     */
    private function generatePassword($length = 12): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*?';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Check user existence.
     *
     * @param string $email
     * @return User|null
     */
    private function checkUserExistence(string $email): ?User
    {
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            return null;
        }
        
        return $user;
    }
}