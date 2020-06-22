<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Game\GameAction;
use App\Events\GameUserJoinedEvent;
use App\Services\Game\GameActionService;
use App\Services\Game\GameService;
use Doctrine\ORM\EntityManagerInterface;

class GameListener
{
    private GameService $gameService;

    private EntityManagerInterface $em;

    private GameActionService $gameActionService;

    public function __construct(EntityManagerInterface $em, GameService $gameService, GameActionService $gameActionService)
    {
        $this->gameService = $gameService;
        $this->em = $em;
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

        $gameActionValues = [
            'team' => $team->getId(),
        ];

        $this->gameActionService->createGameAction(
            $game,
            $gameActionValues,
            GameAction::TEMPLATE_USER_JOINED_TO_GAME, $user,
            true
        );

        if ($game->isFull()) {
            $this->gameService->startGame($game, $user);
        }
    }
}