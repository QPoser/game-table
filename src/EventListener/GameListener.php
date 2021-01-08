<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Events\GameUserJoinedEvent;
use App\Events\GameUserLeavedEvent;
use App\Services\Game\GameActionService;
use App\Services\Game\GameService;

final class GameListener
{
    private GameService $gameService;

    private GameActionService $gameActionService;

    public function __construct(GameService $gameService, GameActionService $gameActionService)
    {
        $this->gameService = $gameService;
        $this->gameActionService = $gameActionService;
    }

    public function onUserJoined(GameUserJoinedEvent $event): void
    {
        $team = $event->getGameTeam();
        $game = $team->getGame();
        $user = $event->getUser();

        if (!$game) {
            return;
        }

        $this->gameActionService->createUserJoinedToGameAction($game, $team, $user);

        if ($game->isFull()) {
            $this->gameService->startGame($game, $user);
        }
    }

    public function onUserLeaved(GameUserLeavedEvent $event): void
    {
        $team = $event->getGameTeam();
        $game = $team->getGame();
        $user = $event->getUser();

        if (!$game) {
            return;
        }

        $this->gameActionService->createUserLeavedFromGameAction($game, $team, $user);
    }
}
