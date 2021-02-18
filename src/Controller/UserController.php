<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\User;
use App\Form\UserRegistrationForm;
use App\Service\UserService;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
     * @param UserService $userService
     *
     * @return JsonResponse|Response
     * @Route("/admin/register/apiUser", name="register_api_user", methods={"GET", "POST"})
     */
    public function registerApiUser(UserService $userService)
    {
        $result = $userService->userRegistration();

        if (empty($result)) {
            return new Response();
        }

        return $this->render(
            "admin/registerApiUser.html.twig", [
                'user' => $result['user'],
                'application' => $result['application'],
//                'builder' => $builder,
//                'form' => $form->createView(),
            ]
        );
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