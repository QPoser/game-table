<?php

declare(strict_types=1);

namespace App\Services\Validation;

use App\Exception\ApiException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationService
{
    private const SKIPPED_MESSAGES = [
        'This value should not be blank.'
    ];

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateEntity(object $entity): void
    {
        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($entity);

        if ($errors !== null) {
            $errorMessages = [];

            foreach ($errors as $error) {
                if (!in_array($error->getMessage(), self::SKIPPED_MESSAGES, true)) {
                    $errorMessages[] = $error->getMessage();
                }
            }

            if (!empty($errorMessages)) {
                throw new ApiException(0, implode(';', $errorMessages));
            }
        }
    }
}
