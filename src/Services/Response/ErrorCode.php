<?php
declare(strict_types=1);

namespace App\Services\Response;

class ErrorCode
{
    public const USER_NOT_FOUND_BY_TOKEN = 0;
    public const USER_ALREADY_EXISTS_IN_DATABASE = 1;
    public const GAME_PASSWORD_IS_INVALID = 2;
    public const GAME_TEAM_NOT_FOUND = 3;
    public const USER_ALREADY_IN_GAME_TEAM = 4;
    public const GAME_TEAM_HAS_NO_SLOT = 5;
    public const TEAM_ID_MUST_BE_SET_FOR_ROOM = 6;
    public const FAIL_ON_CREATE_TEAM_PLAYER = 7;
    public const USER_IS_NOT_IN_GAME = 8;
    public const GAME_TYPE_NOT_FOUND = 9;

    private const MESSAGES = [
        self::USER_NOT_FOUND_BY_TOKEN => 'User not found by token',
        self::USER_ALREADY_EXISTS_IN_DATABASE => 'User already exists in database',
        self::GAME_PASSWORD_IS_INVALID => 'Game password is invalid',
        self::GAME_TEAM_NOT_FOUND => 'Game team not found',
        self::USER_ALREADY_IN_GAME_TEAM => 'User already in game team',
        self::GAME_TEAM_HAS_NO_SLOT => 'Game team has no slot',
        self::TEAM_ID_MUST_BE_SET_FOR_ROOM => 'Team id must be set for room',
        self::FAIL_ON_CREATE_TEAM_PLAYER => 'Fail on create team player',
        self::USER_IS_NOT_IN_GAME => 'User is not in game',
        self::GAME_TYPE_NOT_FOUND => 'Game type not found',
    ];

    public static function getMessage(int $code): ?string
    {
        return self::MESSAGES[$code] ?? null;
    }
}