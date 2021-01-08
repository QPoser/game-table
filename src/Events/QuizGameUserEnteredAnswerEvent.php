<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Game\Quiz\QuizGame;
use Symfony\Contracts\EventDispatcher\Event;

final class QuizGameUserEnteredAnswerEvent extends Event
{
    public const NAME = 'game.quiz.user_entered_answer';

    private QuizGame $game;

    public function __construct(QuizGame $game)
    {
        $this->game = $game;
    }

    public function getGame(): QuizGame
    {
        return $this->game;
    }
}
