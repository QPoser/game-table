<?php
declare(strict_types=1);

namespace App\Services\Response;

class Responser
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    public static function wrapSuccess($data, array $additional = []): array
    {
        return [
            'data' => $data,
            'partials' => $additional,
            'status' => self::STATUS_SUCCESS,
        ];
    }

    public static function wrapError(string $message, int $code): array
    {
        return [
            'message' => $message,
            'code' => $code,
            'status' => self::STATUS_ERROR,
        ];
    }
}