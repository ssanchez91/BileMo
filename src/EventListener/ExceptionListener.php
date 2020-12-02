<?php

namespace App\EventListener;

use App\Exception\ForbiddenException;
use App\Exception\ResourceNoValidateException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use OpenApi\Annotations\Response as AnnotationsResponse;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

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
        $response = $this->prepareNewResponse($response, $exception, $message);        

        // sends the modified response object to the event
        $event->setResponse($response);
    }

    public function prepareNewResponse(Response $response, \Exception $exception, $message):Response
    {
        switch(true) {
            case $exception instanceof HttpExceptionInterface:
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
                $response->setContent(json_encode(['code' => $exception->getStatusCode(), 'message' => $message]));
                break;
            case $exception instanceof JWTDecodeFailureException:
                $response->setContent(json_encode(['code' => 401, 'message' => $message]));
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                break;
            case $exception instanceof ForbiddenException:
                $response->setContent(json_encode(['code' => 403, 'message' => $message]));
                $response->setStatusCode(Response::HTTP_FORBIDDEN);
                break;
            case $exception instanceof ResourceNoValidateException:
                $response->setContent(json_encode(['code' => 404, 'message' => $message]));
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                break;
            default:
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
        return $response;
    }
}
