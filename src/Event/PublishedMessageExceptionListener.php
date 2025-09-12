<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublishedMessageExceptionListener
{
  /**
   * @param ExceptionEvent $event
   */
  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();
    $message = $exception->getMessage();

    if ($exception instanceof NotFoundHttpException)
      $code = Response::HTTP_NOT_FOUND;
    else
      $code = Response::HTTP_UNAUTHORIZED;

    $responseData = [
      'error' => [
        'code' => $code,
        'message' => str_replace('"', ' ', $message)
      ]
    ];

    $event->setResponse(new JsonResponse($responseData, $code));
  }
}
