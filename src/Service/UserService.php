<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\User;
use App\Traits\DataControllerTrait;
use Exception;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Request;

class UserService extends Service
{
    use DataControllerTrait;

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
        $roles = ["ROLE_USER"];

        switch ($request->get('quotaRole')) {
            case 'isAdmin':
                array_push($roles, "ROLE_ADMIN");
                break;
            case 'isVip':
                array_push($roles, "ROLE_VIP");
                break;
            case 'isExternal':
                array_push($roles, "ROLE_EXTERNAL");
                break;
            case 'isPremium':
                array_push($roles, "ROLE_PREMIUM");
                break;
            case 'isFreemium':
                array_push($roles, "ROLE_FREEMIUM");
                break;
            case 'isHydrator':
                array_push($roles, "ROLE_HYDRATOR");
                break;
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
        $application->setReferer($this->extractDomainUri($request->get('referer')));

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
        return bin2hex(random_bytes(16));
    }

    /**
     * Generate random password.
     *
     * @param int $length
     *
     * @return string
     * @throws RandomException
     */
    private function generatePassword(int $length = 18): string
    {
        if ($length < 4) {
            throw new \InvalidArgumentException('Password length must be at least 4');
        }

        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';
        $symbols = '!@#$%&*?';

        $all = $lower . $upper . $digits . $symbols;

        $password = [];
        $password[] = $lower[random_int(0, strlen($lower) - 3)];
        $password[] = $upper[random_int(0, strlen($upper) - 3)];
        $password[] = $digits[random_int(0, strlen($digits) - 3)];
        $password[] = $symbols[random_int(0, strlen($symbols) - 3)];

        for ($i = 4; $i < $length; $i++) {
            $password[] = $all[random_int(0, strlen($all) - 1)];
        }

        shuffle($password);

        return implode('', $password);
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

    private function parseReferUri(string $uri): string
    {
        $pattern = "/^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n?]+)/";
        preg_match($pattern, $uri, $matches);

        return $matches[1];
    }
}
