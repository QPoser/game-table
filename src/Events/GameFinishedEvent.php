<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Game\Game;
use Symfony\Contracts\EventDispatcher\Event;

final class GameFinishedEvent extends Event
{
    public const NAME = 'game.finished';

    private Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
