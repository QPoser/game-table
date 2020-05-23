<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Exception\AppException;
use App\Exception\CustomResponseException;
use App\Services\Response\Responser;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    private const JSON_CONTENT_TYPE = 'application/json';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

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

        $response = new RedirectResponse($request->headers->get('referer'));

        $event->setResponse($response);
    }

    private function handleApiException(CustomResponseException $exception, ExceptionEvent $event): void
    {
        $response = new JsonResponse(Responser::wrapError($exception->getMessage(), $exception->getCode()));

        $event->setResponse($response);
    }

    private function isApiRequest(Request $request): bool
    {
        return in_array(self::JSON_CONTENT_TYPE, $request->getAcceptableContentTypes(), true);
    }
}