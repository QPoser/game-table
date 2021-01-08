<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\QuizGame;
use Symfony\Contracts\EventDispatcher\Event;

final class QuizGamePhaseFinishedEvent extends Event
{
    public const NAME = 'game.quiz.phase_finished';

    private QuizGame $game;

    private BasePhase $phase;

    public function __construct(QuizGame $game, BasePhase $phase)
    {
        $this->game = $game;
    }

    public function getGame(): QuizGame
    {
        return $this->game;
    }

    public function getPhase(): BasePhase
    {
        return $this->phase;
    }
}
