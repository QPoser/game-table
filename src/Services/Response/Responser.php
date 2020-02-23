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
}