<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Dto\RequestDto\RequestDTOInterface;
use App\Exception\ApiException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        try {
            $reflection = new ReflectionClass($argument->getType());
        } catch (\ReflectionException $e) {
            return false;
        }

        return $reflection->implementsInterface(RequestDTOInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $dto = new $class($request);

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errorsList = [];

            foreach ($errors as $key => $error) {
                $errorsList[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new BadRequestHttpException(json_encode($errorsList));
        }

        yield $dto;
    }
}