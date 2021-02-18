<?php

namespace App\Service;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
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

    public function userRegistration()
    {
        $authenticator = new AuthenticatorService($this->request, $this->entityManager);
        $application = $authenticator->getApplicationByToken($this->request->query->get('realtAuthToken'));

        if (!empty($application)) {
            $isAdmin = $authenticator->applicationHaveAdminRights($application);
        }

        if (!($application Instanceof Application) || !$isAdmin) {
            return [];
        }

        $em = $this->entityManager;
        $user = $application = "";

//        $user = new User();
//        $form = $this->createForm(UserRegistrationForm::class, $user);
//        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
        if ($this->request->getMethod() == 'POST') {
            $adminToken = $this->request->get('adminToken');
            $isAdmin = $this->isAdmin($adminToken);

            if (!$isAdmin) {
                $response = new JsonResponse();

                $response->setData(["status" => "error", "message" => "User is not granted"])
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            if ($this->request->get('isAdmin')) {
                $roles = ["ROLE_USER", "ROLE_ADMIN"];
            } else {
                $roles = ["ROLE_USER"];
            }

//            $user = $form->getData();

//            $user = new User();
//            $user->setEmail($rqt->get('email'));
//            $user->setRoles($roles);
//            $user->setPassword($this->generatePassword());
//            $user->setUsername($rqt->get('username'));
//            $user->setEthereumAddress($rqt->get('ethereumAddress'));
//            $em->persist($user);

            $application = new Application();
            $application->setUser($user);
            $application->setName($this->request->get('appName'));
            $application->setApiToken($this->generateToken());
            $em->persist($application);

            $em->flush();
        }

        return $response = ['user' => $user, 'application' => $application];
    }

    /**
     * Generate unique token.
     *
     * @return string
     * @throws Exception
     */
    private function generateToken()
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
    private function generateUuid(int $length)
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
    private function generatePassword($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*?';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}