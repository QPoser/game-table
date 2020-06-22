<?php
declare(strict_types=1);

namespace App\Events;

use App\Entity\Game\Game;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class GameUserJoinedEvent extends Event
{
    public const NAME = 'game.user.joined';

    private Game $game;

    private User $user;

    public function __construct(Game $game, User $user)
    {
        $this->game = $game;
        $this->user = $user;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}