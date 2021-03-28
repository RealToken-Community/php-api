<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Quota;
use App\Entity\QuotaHistory;
use App\Entity\QuotaLimitations;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * Class AuthenticatorService
 * @package App\Service
 */
class AuthenticatorService extends Service
{
    /**
     * Check admin rights.
     *
     * @param string|null $apiKey
     */
    public function checkAdminRights(?string $apiKey)
    {
        if (!empty($apiKey)) {
            $application = $this->getApplicationByToken($apiKey);
        }

        if (empty($apiKey) || !$this->applicationHaveAdminRights($application)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, "Unauthorized");
        }
    }

    /**
     * Get application by token.
     *
     * @param string $apiKey
     *
     * @return Application
     */
    public function getApplicationByToken(string $apiKey): Application
    {
        $applicationRepository = $this->em->getRepository(Application::class);

        /** @var Application $application */
        $application = $applicationRepository->findOneBy(['apiToken' => $apiKey]);

        if (is_null($application)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Api token not found");
        }

        $this->checkUserQuota($application->getQuota(), $application);

        $this->consumeQuota($application, $apiKey);

        return $application;
    }

    /**
     * Check if Application have admin rights.
     *
     * @param Application $application
     *
     * @return bool
     */
    public function applicationHaveAdminRights(Application $application): bool
    {
        return in_array("ROLE_ADMIN", $application->getUser()->getRoles());
    }

    /**
     * Check user authentication.
     *
     * @param string|null $apiKey
     *
     * @return array
     */
    public function checkCredentials(string $apiKey = null): array
    {
        $credentials = ['isAdmin' => false, 'isAuth' => false];

        if (!empty($apiKey)) {
            $applicationRepository = $this->em->getRepository(Application::class);
            $application = $applicationRepository->findOneBy(['apiToken' => $apiKey]);

            if (!($application Instanceof Application)) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid API Token');
            }

            $user = $application->getUser();
            $roles = $user->getRoles();

            if (in_array("ROLE_ADMIN", $roles)) {
                $credentials = ['isAdmin' => true];
            }

            $credentials['isAuth'] = true;
        }

        return $credentials;
    }

    /**
     * Increment API quota.
     *
     * @param Application $application
     * @param string $apiKey
     */
    private function consumeQuota(Application $application, string $apiKey)
    {
        $quotaService = $this->em->getRepository(Quota::class);
        $quota = $quotaService->findOneBy(['application' => $application]);

        if (!$quota) {
            $quota = new Quota();
            $quota->setApplication($application);
        }
        
        $quota->setIncrement();
        $this->em->persist($quota);
        $this->em->flush();

        if ($application->getQuota() === null) {
            $application->setQuota($quota);
            $this->em->persist($application);
            $this->em->flush();
        }

        // Tmp rate limiter
        $this->addQuotaHistory($application);

        // TODO : Sf Rate Limiter Quota
        $user = $application->getUser();
        $roles = $user->getRoles();
        $this->getUserLimiter($roles, $apiKey);
    }

    /**
     * API RateLimiter anonymous.
     *
     * @param RateLimiterFactory $factory
     * @param string $apiKey
     */
    private function rateLimiter(RateLimiterFactory $factory, string $apiKey)
    {
        // create a limiter based on a unique identifier of the client
        // (e.g. the client's IP address, a username/email, an API key, etc.)
        $limiter = $factory->create($apiKey);

        $limit = $limiter->consume(5);
        $headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp(),
            'X-RateLimit-Limit' => $limit->getLimit(),
        ];

        if (false === $limit->isAccepted()) {
            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS, null, null, $headers);
        }

        // the argument of consume() is the number of tokens to consume
        // and returns an object of type Limit
        if (false === $limiter->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

//        $limiter->reserve(1)->wait();

//        $response = new Response(null);
//        $response->headers->add($headers);
//
//        return $response;

        // you can also use the ensureAccepted() method - which throws a
        // RateLimitExceededException if the limit has been reached
        // $limiter->consume(1)->ensureAccepted();
    }

//    /**
//     * API RateLimiter registered user.
//     *
//     * @param RequestStack $requestStack
//     * @param RateLimiterFactory $authenticatedApiLimiter
//     */
//    public function registerUser(RequestStack $requestStack, RateLimiterFactory $authenticatedApiLimiter)
//    {
//        $request = $requestStack->getCurrentRequest();
//        $apiKey = $request->headers->get('apikey');
//        $limiter = $authenticatedApiLimiter->create($apiKey);
//
//        // this blocks the application until the given number of tokens can be consumed
//        $limiter->reserve(1)->wait();
//
//        // optional, pass a maximum wait time (in seconds), a MaxWaitDurationExceededException
//        // is thrown if the process has to wait longer. E.g. to wait at most 20 seconds:
//        //$limiter->reserve(1, 20)->wait();
//    }

    /**
     * Add quota history.
     *
     * @param Application $application
     */
    private function addQuotaHistory(Application $application) {
        $quota = $application->getQuota();

        $quotaHistory = new QuotaHistory();
        $quotaHistory->setQuota($quota);
        $quotaHistory->setAccessTime(new DateTime());

        $this->em->persist($quotaHistory);
        $this->em->flush();
    }

    /**
     * Get last quota history.
     *
     * @param Quota $quotaHistory
     * @param Application $application
     */
    private function checkUserQuota(Quota $quotaHistory, Application $application)
    {
        $quotaHistoryRepository = $this->em->getRepository(QuotaHistory::class);
        $nbRequest = $quotaHistoryRepository->findLastUsage($quotaHistory, new DateTime("60 minutes ago"));

        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
        $roles = $application->getUser()->getRoles();

        /** @var QuotaLimitations $quotaLimitation */
        $quotaLimitation = $quotaLimitationsRepository->findOneBy(['role' => $roles]);

        switch (true) {
            case $nbRequest > $quotaLimitation->getLimitPerYear():
            case $nbRequest > $quotaLimitation->getLimitPerMonth():
            case $nbRequest > $quotaLimitation->getLimitPerWeek():
            case $nbRequest > $quotaLimitation->getLimitPerDay():
            case $nbRequest > $quotaLimitation->getLimitPerHour():
            case $nbRequest > $quotaLimitation->getLimitPerMinute():
                throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS, 'API quota exceeded');
        }
    }

    /**
     * Define limiter factory.
     *
     * @param array $roles
     * @param string $apiKey
     */
    private function getUserLimiter(array $roles, string $apiKey)
    {
        if (($key = array_search("ROLE_USER", $roles)) !== false) {
            unset($roles[$key]);
        }

        if (empty(array_values($roles))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, "Not admin user");
        }

        $role = array_values($roles)[0];

        switch ($role) {
            case 'ROLE_FREEMIUM':
                $id = 'freemiumApiLimiter';
                $limit = 2;
                break;
            case 'ROLE_PREMIUM':
                $id = 'premiumApiLimiter';
                $limit = 3;
                break;
            case 'ROLE_EXTERNAL':
                $id = 'externalApiLimiter';
                $limit = 4;
                break;
            case 'ROLE_VIP':
                $id = 'vipApiLimiter';
                $limit = 5;
                break;
            case 'ROLE_ADMIN':
                $id = 'adminApiLimiter';
                $limit = 6;
                break;
            default:
                $id = 'anonymousApiLimiter';
                $limit = 1;
                break;
        }

        $factory = new RateLimiterFactory(
            [ 'id' => $id,
                'policy' => 'sliding_window',
                'limit' => $limit,
                'interval' => '60 minutes',
//                'rate' => ['interval' => '60 minutes']
            ], new InMemoryStorage());

        $this->rateLimiter($factory, $apiKey);
    }

//    private function checkQuotaRate(array $roles)
//    {
//        if (($key = array_search("ROLE_USER", $roles)) !== false) {
//            unset($roles[$key]);
//        }
//
//        $roles = array_values($roles);
//        $role = $roles[0];
//
//        /** @var QuotaLimitationsRepository $quotaLimitationsRepository */
//        $quotaLimitationsRepository = $this->em->getRepository(QuotaLimitations::class);
//        $limitations = $quotaLimitationsRepository->findOneBy(["role" => $role]);
//
//        $limitations->getLimitPerMinute();
//
//        // TODO : request to get all last quota array from user
//
//        $tmp = null;
//    }
}
