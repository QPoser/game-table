<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\CustomResponseException;
use App\Services\Response\Responser;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

final class ExceptionListener
{
    private const JSON_CONTENT_TYPE = 'application/json';

    private ContainerInterface $container;

    private SerializerInterface $serializer;

    public function __construct(ContainerInterface $container, SerializerInterface $serializer)
    {
        $this->container = $container;
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if ($exception instanceof BadRequestHttpException) {
            if ($this->isApiRequest($request)) {
                $exception = new ApiException((int) $exception->getCode(), $exception->getMessage());
            } else {
                $exception = new AppException((int) $exception->getCode(), $exception->getMessage());
            }
        }

        if ($exception instanceof CustomResponseException) {
            if ($this->isApiRequest($request)) {
                $this->handleApiException($exception, $event);
            } else {
                $this->handleAppException($request, $exception, $event);
            }

            return;
        }
    }

    private function handleAppException(Request $request, CustomResponseException $exception, ExceptionEvent $event): void
    {
        $this->container->get('session')->getFlashBag()->add('error', $exception->getMessage());
        $referer = $request->headers->get('referer');

        $response = new RedirectResponse($referer ?? '/');

        $event->setResponse($response);
    }

    private function handleApiException(CustomResponseException $exception, ExceptionEvent $event): void
    {
        $data = $this->serializer->serialize(
            Responser::wrapError($exception->getMessage(), (int) $exception->getCode()),
            'json'
        );

        $event->setResponse(new JsonResponse($data, Response::HTTP_OK, [], true));
    }

    private function isApiRequest(Request $request): bool
    {
        return in_array(self::JSON_CONTENT_TYPE, $request->getAcceptableContentTypes(), true);
    }
}
