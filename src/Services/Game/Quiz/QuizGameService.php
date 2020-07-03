<?php
declare(strict_types=1);

namespace App\Services\Game\Quiz;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Game\GameActionService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;

class QuizGameService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private GameNotificationTemplateHelper $gameNTH;

    private GameActionService $gameActionService;

    private QuizPhaseService $quizPhaseService;

    public function __construct(
        EntityManagerInterface $em,
        ValidationService $validator,
        GameNotificationTemplateHelper $gameNTH,
        GameActionService $gameActionService,
        QuizPhaseService $quizPhaseService
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->gameNTH = $gameNTH;
        $this->gameActionService = $gameActionService;
        $this->quizPhaseService = $quizPhaseService;
    }

    public function createGame(string $title, ?User $creator = null,?string $password = null): QuizGame
    {
        $this->em->beginTransaction();
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

        $this->quizPhaseService->createPhase(BasePhase::TYPE_QUESTIONS, $game);

        try {
            $this->em->commit();
        } catch (\Throwable $exception) {
            throw $exception;
        }

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

    public function startGame(QuizGame $game, ?User $user = null): void
    {
        $game->setStatus(Game::STATUS_STARTED);
        $game->setFirstTeamPlayerTurn();

        $this->em->persist($game);
        $this->em->flush($game);

        $this->quizPhaseService->createPhase(BasePhase::TYPE_QUESTIONS, $game);

        $this->gameActionService->createGameStartedActions($game);
        $this->gameNTH->createGameStartedNotifications($game);
    }

    public function addPhase(QuizGame $game, string $phaseType, User $user): void
    {
        $this->quizPhaseService->createPhase($phaseType, $game);

        $this->gameActionService->createQuizGamePhaseChosenActions($game, $user);

        if ($game->getPhases()->count() === QuizGame::PHASES_COUNT) {
            $this->startGamePlaying($game);
        } else {
            $game->setSecondTeamPlayerTurn();
            $this->em->flush();
        }
    }

    public function startGamePlaying(QuizGame $game): void
    {
        $game->setGameStatus(QuizGame::GAME_STATUS_PLAYING);

        /** @var BasePhase $phase */
        $phase = $game->getPhases()->first();
        $phase->play();

        $this->em->flush();

        $this->gameActionService->createQuizGamePlayingActions($game);
    }
}