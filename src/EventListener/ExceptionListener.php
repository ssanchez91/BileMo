<?php

namespace App\EventListener;

use App\Exception\ForbiddenException;
use App\Exception\ResourceNoValidateException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = $exception->getMessage();

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $response->setContent(json_encode(['code' => $exception->getStatusCode(), 'message' => $exception->getMessage()]));
        } else if ($exception instanceof JWTDecodeFailureException) {
            $response->setContent(json_encode(['code' => 401, 'message' => $exception->getMessage()]));
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        } else if ($exception instanceof ForbiddenException) {
            $response->setContent(json_encode(['code' => 403, 'message' => $exception->getMessage()]));
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
        } else if ($exception instanceof ResourceNoValidateException) {
            $response->setContent(json_encode(['code' => 404, 'message' => $exception->getMessage()]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
