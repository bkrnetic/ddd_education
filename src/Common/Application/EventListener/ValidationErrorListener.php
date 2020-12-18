<?php

declare(strict_types=1);

namespace Common\Application\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ValidationErrorListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY
            && $response instanceof JsonResponse
            && \is_string($response->getContent())
        ) {
            $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $response->setJson(json_encode($content, JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR));
        }
    }
}
