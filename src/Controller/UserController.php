<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Register new Api User.
     *
     * @param Request $request
     *
     * @Route("/register/apiUser", name="register_api_user", methods={"GET", "POST"})
     *
     * @return JsonResponse|Response
     */
    public function registerApiUser(Request $request)
    {
        $em = $this->entityManager;

        $user = $application = "";

        if ($request->getMethod() == 'POST') {
            $rqt = $request->request;

            $adminToken = $rqt->get('adminToken');
            $isAdmin = $this->isAdmin($adminToken);

            if (!$isAdmin) {
                $response = new JsonResponse();

                $response->setData(["status" => "error", "message" => "User is not granted"])
                        ->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $response;
            }

            if ($rqt->get('isAdmin')) {
                $roles = ["ROLE_USER", "ROLE_ADMIN"];
            } else {
                $roles = ["ROLE_USER"];
            }

            $user = new User();
            $user->setEmail($rqt->get('email'));
            $user->setRoles($roles);
            $user->setPassword($this->generatePassword());
            $user->setUsername($rqt->get('username'));
            $user->setEthereumAddress($rqt->get('ethereumAddress'));
            $em->persist($user);

            $application = new Application();
            $application->setUser($user);
            $application->setName($rqt->get('appName'));
            $application->setApiToken($this->generateToken());
            $em->persist($application);

            $em->flush();
        }

        return $this->render(
            "registerApiUser.html.twig", [
                'user' => $user,
                'application' => $application,
            ]
        );
    }

    private function isAdmin(string $apiKey)
    {
        $em = $this->entityManager;
        $applicationRepository = $em->getRepository(Application::class);
        $application = $applicationRepository->findOneBy(['apiToken' => $apiKey]);

        $user = $application->getUser();
        $roles = $user->getRoles();

        if (!in_array("ROLE_ADMIN", $roles)) {
            return false;
        }

        return true;
    }

    /**
     * Generate unique token.
     *
     * @return string
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
     */
    private function generateUuid(int $length)
    {
        return substr(bin2hex(random_bytes(15)), 0, $length);
    }

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

//    public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, LoggerInterface $logger)
//    {
//        $this->jwtManager = $jwtManager;
//        $this->tokenStorageInterface = $tokenStorageInterface;
//        $this->logger = $logger;
//    }
//
//    /**
//     * Créé un nouveau User et l'enregistre en BdD avec son password encrypté
//     *
//     * @Route("/register", name="app_register", methods={"POST"})
//     */
//    public function register(Request $request, UserPasswordEncoderInterface $encoder)
//    {
//        $this->logger->warning('AuthController register');
//        $em = $this->getDoctrine()->getManager();
//
//        $username = $request->request->get('username');
//        $password = $request->request->get('password');
//
//        $user = new User($username);
//        $user->setPassword($encoder->encodePassword($user, $password));
//        $em->persist($user);
//        $em->flush();
//
//        return new Response(sprintf('User %s successfully created', $user->getUsername()));
//    }
//
//    /**
//     * @Route("/login", name="app_login", methods={"POST"})
//     */
//    public function login()
//    {
//        $this->logger->warning('AuthController login');
//    }
//
//    /**
//     * @Route("/logged_user", methods={"GET"})
//     */
//    public function loggedUser(Request $request)
//    {
//        try {
//            // Récupère le token utilisé
//            $token = $this->tokenStorageInterface->getToken();
//            // Récupère l'utilisateur lié à ce token
//            $user = $this->jwtManager->decode($token);
//
//            $this->logger->warning('AuthController: user logged as', ['user' => $user]);
//
//            if ($user) {
//                return new Response(
//                    json_encode(array("user" => $user)),
//                    Response::HTTP_OK,
//                    ['content-type' => 'application/json']);
//            } else {
//                return new Response(
//                    json_encode(['error' => 'Not logged']),
//                    Response::HTTP_NO_CONTENT,
//                    ['content-type' => 'application/json']);
//            }
//        }
//        catch(\Exception $e) {
//            $this->logger->error('AuthController: user logged error', ['error' => $e]);
//
//            return new Response(
//                json_encode(['error' => $e]),
//                Response::HTTP_NO_CONTENT,
//                ['content-type' => 'application/json']);
//        }
//    }
}