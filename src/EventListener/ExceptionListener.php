<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /** @var bool */
    protected $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function process(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $exception = $event->getException();
        $event->stopPropagation();

        $response = [
            'errors' => [
                ['type' => 'exception'] + ($this->debug ? [
                    'class' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                ] : [
                    'message' => 'Something went wrong',
                ])
            ],
        ];

        $event->setResponse($response instanceof Response ? $response : new JsonResponse($response));
    }
}
