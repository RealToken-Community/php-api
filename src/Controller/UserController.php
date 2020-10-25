<?php

namespace App\Controller;

class UserController
{
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