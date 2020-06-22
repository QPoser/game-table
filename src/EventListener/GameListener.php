<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Events\GameUserJoinedEvent;
use App\Services\Game\GameService;
use Doctrine\ORM\EntityManagerInterface;

class GameListener
{
    private GameService $gameService;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, GameService $gameService)
    {
        $this->gameService = $gameService;
        $this->em = $em;
    }

    public function onUserJoined(GameUserJoinedEvent $event): void
    {
        $game = $event->getGame();
        $user = $event->getUser();

        if ($game->isFull()) {
            $this->gameService->startGame($game, $user);
        }
    }
}