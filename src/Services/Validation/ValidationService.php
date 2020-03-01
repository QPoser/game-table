<?php
declare(strict_types=1);

namespace App\Services\Validation;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateEntity(object $entity): void
    {
        $errors = $this->validator->validate($entity);

        if (!empty($errors)) {
            // Handle for api
        }
    }
}