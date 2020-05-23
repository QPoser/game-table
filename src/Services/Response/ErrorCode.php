<?php
declare(strict_types=1);

namespace App\Services\Response;

class ErrorCode
{
    public const USER_NOT_FOUND_BY_TOKEN = 0;
    public const USER_ALREADY_EXISTS_IN_DATABASE = 1;
    public const ROOM_PASSWORD_IS_INVALID = 2;

    private const MESSAGES = [
        self::USER_NOT_FOUND_BY_TOKEN => 'User not found by token',
        self::USER_ALREADY_EXISTS_IN_DATABASE => 'User already exists in database',
        self::ROOM_PASSWORD_IS_INVALID => 'Room password is invalid',
    ];

    public static function getMessage(int $code): ?string
    {
        return self::MESSAGES[$code] ?? null;
    }
}