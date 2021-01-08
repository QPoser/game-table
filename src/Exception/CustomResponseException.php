<?php

declare(strict_types=1);

namespace App\Exception;

use App\Services\Response\ErrorCode;
use Exception;
use Throwable;

abstract class CustomResponseException extends Exception
{
    public function __construct(int $code, ?string $message = null, ?Throwable $previous = null)
    {
        if (!$message) {
            $message = ErrorCode::getMessage($code);
        }

        parent::__construct((string) $message, $code, $previous);
    }
}
