<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\Game\Team\GameTeamPlayer;
use App\Entity\User;
use App\Events\GameFinishedEvent;
use App\Events\GameUserJoinedEvent;
use App\Events\GameUserLeavedEvent;
use App\Exception\AppException;
use App\Services\Game\Quiz\QuizGameService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class GameService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private GameNotificationTemplateHelper $gameNTH;

    private QuizGameService $quizGameService;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        ValidationService $validator,
        QuizGameService $quizGameService,
        GameNotificationTemplateHelper $gameNTH,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->gameNTH = $gameNTH;
        $this->quizGameService = $quizGameService;
        $this->dispatcher = $dispatcher;
    }

    public function createGame(string $title, string $type, ?User $creator = null, ?string $password = null): Game
    {
        $this->em->beginTransaction();

        switch ($type) {
            case Game::TYPE_QUIZ:
                $game = $this->quizGameService->createGame($title, $creator, $password);
            break;

            default:
                throw new AppException(ErrorCode::USER_IS_NOT_IN_GAME);
            break;
        }

        $this->em->commit();

        if ($creator) {
            $this->gameNTH->createGameCreatedNotifications($creator, $game);
        }

        return $game;
    }

    public function createTeamPlayer(User $user, GameTeam $team): GameTeamPlayer
    {
        $this->em->beginTransaction();

        if (!$team->hasSlot()) {
            throw new AppException(ErrorCode::GAME_TEAM_HAS_NO_SLOT);
        }

        $game = $team->getGame();

        if (!$game) {
            throw new RuntimeException('Team has no active game');
        }

        if (!$team->hasUser($user) && $game->hasUser($user)) {
            $this->leaveGame($user, $game);
        }

        $teamPlayer = new GameTeamPlayer();
        $teamPlayer->setUser($user);
        $team->addPlayer($teamPlayer);

        $this->validator->validateEntity($teamPlayer);

        $this->em->persist($teamPlayer);
        $this->em->flush();

        try {
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw new AppException(ErrorCode::FAIL_ON_CREATE_TEAM_PLAYER);
        }

        return $teamPlayer;
    }

    public function leaveGame(User $user, Game $game): void
    {
        $teamPlayer = $game->getTeamPlayerByUser($user);

        if (!$teamPlayer) {
            throw new AppException(ErrorCode::USER_IS_NOT_IN_GAME);
        }

        $team = $teamPlayer->getTeam();

        if (!$team) {
            throw new AppException(ErrorCode::USER_IS_NOT_IN_GAME);
        }

        $this->em->remove($teamPlayer);
        $this->em->flush();

        $event = new GameUserLeavedEvent($team, $user);
        $this->dispatcher->dispatch($event, GameUserLeavedEvent::NAME);
    }

    public function joinGame(User $user, Game $game, ?int $teamId, ?string $password): void
    {
        if (!$game->hasUser($user) && $game->getPassword() && (!$password || $game->getPassword() !== $password)) {
            throw new AppException(ErrorCode::GAME_PASSWORD_IS_INVALID);
        }

        $userCurrentGame = $this->em->getRepository(Game::class)->getCurrentUserGame($user);

        if ($userCurrentGame) {
            throw new AppException(ErrorCode::USER_ALREADY_HAS_GAME_IN_PROGRESS);
        }

        if ($game::STRICT_TEAMS) {
            if (!$teamId) {
                throw new AppException(ErrorCode::TEAM_ID_MUST_BE_SET_FOR_ROOM);
            }

            $team = $game->getTeamById($teamId);

            if ($team && $team->hasUser($user)) {
                throw new AppException(ErrorCode::USER_ALREADY_IN_GAME_TEAM);
            }
        } else {
            $team = $game->getTeamWithSlot();
        }

        if (!$team) {
            throw new AppException(ErrorCode::GAME_TEAM_NOT_FOUND);
        }

        $this->createTeamPlayer($user, $team);

        $event = new GameUserJoinedEvent($team, $user);
        $this->dispatcher->dispatch($event, GameUserJoinedEvent::NAME);
    }

    public function startGame(Game $game, ?User $user = null): void
    {
        if ($game instanceof QuizGame) {
            $this->quizGameService->startGame($game, $user);
        }

        throw new AppException(ErrorCode::GAME_TYPE_NOT_FOUND);
    }

    public function finishGame(Game $game): void
    {
        $game->setStatus(Game::STATUS_FINISHED);
        $this->em->flush();

        $event = new GameFinishedEvent($game);
        $this->dispatcher->dispatch($event, GameFinishedEvent::NAME);
    }
}
