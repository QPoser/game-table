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
    public const INCORRECT_MESSAGE_TYPE = 10;
    public const INCORRECT_GAME_ACTION_TYPE = 11;
    public const USER_ALREADY_HAS_GAME_IN_PROGRESS = 12;
    public const QUIZ_GAME_PHASE_DOES_NOT_EXISTS = 13;
    public const QUIZ_GAME_HAS_MAX_PHASES = 14;
    public const QUIZ_GAME_HAS_NO_CURRENT_PHASE = 15;
    public const QUIZ_GAME_PHASE_HAS_NO_CURRENT_QUESTION = 16;
    public const QUIZ_GAME_QUESTION_HAS_NO_THIS_VARIANT = 17;

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
        self::INCORRECT_MESSAGE_TYPE => 'Incorrect message type',
        self::INCORRECT_GAME_ACTION_TYPE => 'Incorrect game action type',
        self::USER_ALREADY_HAS_GAME_IN_PROGRESS => 'User already has game in progress',
        self::QUIZ_GAME_PHASE_DOES_NOT_EXISTS => 'Quiz game phase does not exists',
        self::QUIZ_GAME_HAS_MAX_PHASES => 'Quiz game has max count of phases',
        self::QUIZ_GAME_HAS_NO_CURRENT_PHASE => 'Quiz game has no current phase in progress',
        self::QUIZ_GAME_PHASE_HAS_NO_CURRENT_QUESTION => 'Quiz game phase has no current question',
        self::QUIZ_GAME_QUESTION_HAS_NO_THIS_VARIANT => 'Quiz game question has no this variant',
    ];

    public static function getMessage(int $code): ?string
    {
        return self::MESSAGES[$code] ?? null;
    }
}