<?php

declare(strict_types=1);

namespace App\Services\Response;

use App\Dto\ResponseDto\ErrorResponseDTO;
use App\Dto\ResponseDto\SuccessResponseDTO;

final class Responser
{
    public static function wrapSuccess($data, array $additional = []): SuccessResponseDTO
    {
        return new SuccessResponseDTO($data, $additional);
    }

    public static function wrapError(string $message, int $code): ErrorResponseDTO
    {
        return new ErrorResponseDTO($message, $code);
    }
}
