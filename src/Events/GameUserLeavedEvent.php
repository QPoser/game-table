<?php
declare(strict_types=1);

namespace App\Events;

use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class GameUserLeavedEvent extends Event
{
    public const NAME = 'game.user.leaved';

    private GameTeam $team;

    private User $user;

    public function __construct(GameTeam $team, User $user)
    {
        $this->team = $team;
        $this->user = $user;
    }

    public function getGameTeam(): GameTeam
    {
        return $this->team;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}