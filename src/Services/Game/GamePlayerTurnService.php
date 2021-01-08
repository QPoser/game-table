<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Entity\Game\Game;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class GamePlayerTurnService
{
    private EntityManagerInterface $em;

    private GameActionService $gameActionService;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        GameActionService $gameActionService,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->gameActionService = $gameActionService;
        $this->dispatcher = $dispatcher;
    }

    public function setFirstTeamPlayerTurn(Game $game): void
    {
        $game->setFirstTeamPlayerTurn();
        $this->em->flush();
        $this->gameActionService->createGameTurnsChangedAction($game);
    }

    public function setSecondTeamPlayerTurn(Game $game): void
    {
        $game->setSecondTeamPlayerTurn();
        $this->em->flush();
        $this->gameActionService->createGameTurnsChangedAction($game);
    }

    public function setPlayersTurnInEveryTeam(Game $game, int $playerIndex): void
    {
        $game->setPlayersTurnInEveryTeam($playerIndex);
        $this->em->flush();
        $this->gameActionService->createGameTurnsChangedAction($game);
    }

    public function disablePlayerTurnByUser(Game $game, User $user): void
    {
        $game->disablePlayerTurnByUser($user);
        $this->em->flush();
        $this->gameActionService->createGameTurnsChangedAction($game);
    }

    public function disablePlayersTurns(Game $game): void
    {
        $game->disablePlayersTurns();
        $this->em->flush();
        $this->gameActionService->createGameTurnsChangedAction($game);
    }

    public function updatePlayersTurnInEveryTeam(Game $game): void
    {
        $game->updatePlayersTurnInEveryTeam();
        $this->em->flush();
        $this->gameActionService->createGameTurnsChangedAction($game);
    }
}
