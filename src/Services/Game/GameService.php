<?php
declare(strict_types=1);

namespace App\Services\Game;

use App\Entity\Game\Game;
use App\Entity\Game\GamePlayer;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\Game\Team\GameTeamPlayer;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Game\Quiz\QuizGameService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private GameNotificationTemplateHelper $gameNTH;

    private QuizGameService $quizGameService;

    public function __construct(
        EntityManagerInterface $em,
        ValidationService $validator,
        QuizGameService $quizGameService,
        GameNotificationTemplateHelper $gameNTH
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->gameNTH = $gameNTH;
        $this->quizGameService = $quizGameService;
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

        if (!$team->hasUser($user) && $team->getGame()->hasUser($user)) {
            $this->leaveGame($user, $team->getGame());
        }

        $teamPlayer = new GameTeamPlayer();
        $teamPlayer->setUser($user);
        $teamPlayer->setTeam($team);

        $this->validator->validateEntity($teamPlayer);

        $this->em->persist($teamPlayer);
        $this->em->flush($teamPlayer);

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

        $this->em->remove($teamPlayer);
        $this->em->flush();
    }

    public function joinGame(User $user, Game $game, ?int $teamId, ?string $password): void
    {
        if (!$game->hasUser($user) && $game->getPassword() && (!$password || $game->getPassword() !== $password)) {
            throw new AppException(ErrorCode::GAME_PASSWORD_IS_INVALID);
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
    }
}