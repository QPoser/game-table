<?php
declare(strict_types=1);

namespace App\Services\Game\Quiz;

use App\Entity\Game\Game;
use App\Entity\Game\GameAction;
use App\Entity\Game\GamePlayer;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Services\Game\GameActionService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class QuizGameService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private GameNotificationTemplateHelper $gameNTH;

    private GameActionService $gameActionService;

    private RouterInterface $router;

    public function __construct(
        EntityManagerInterface $em,
        ValidationService $validator,
        GameNotificationTemplateHelper $gameNTH,
        GameActionService $gameActionService,
        RouterInterface $router
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->gameNTH = $gameNTH;
        $this->gameActionService = $gameActionService;
        $this->router = $router;
    }

    public function createGame(string $title, ?User $creator = null,?string $password = null): QuizGame
    {
        $game = new QuizGame();

        $game->setTitle($title);
        $game->setPassword($password);
        $game->setAccess(Game::ACCESS_PUBLIC);
        $game->setCreator($creator);
        $game->setAutoCreated($creator ? false : true);

        $this->validator->validateEntity($game);
        $this->prepareTeams($game);

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }

    private function prepareTeams(QuizGame $game): void
    {
        for ($i = 1; $i <= QuizGame::MAX_TEAMS; $i++) {
            $team = new GameTeam();

            $game->addTeam($team);
            $team->setSlots(QuizGame::DEFAULT_SLOTS_IN_TEAM);

            $this->em->persist($team);

            $team->setTitle('Team ' . $team->getId());
        }
    }

    public function startGame(Game $game, ?User $user = null): void
    {
        $game->setStatus(Game::STATUS_STARTED);

        $this->em->persist($game);
        $this->em->flush($game);

        $gameActionValues = [
            'gameId' => $game->getId(),
            'type' => $game->getType(),
            'url' => $this->router->generate('api.games.visit', ['id' => $game->getId()]),
        ];

        $this->gameActionService->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_GAME_STARTED, $user);
        $this->gameNTH->createGameStartedNotifications($game);
    }
}