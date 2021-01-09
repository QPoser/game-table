<?php

declare(strict_types=1);

namespace App\Services\Game;

use App\AmqpMessages\AmqpGameAction;
use App\Entity\Game\Game;
use App\Entity\Game\GameAction;
use App\Entity\Game\Quiz\Phase\AnswerInterface;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Response\ErrorCode;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Routing\RouterInterface;

final class GameActionService
{
    private EntityManagerInterface $em;

    private MessageBusInterface $messageBus;

    private RouterInterface $router;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $messageBus, RouterInterface $router)
    {
        $this->em = $em;
        $this->messageBus = $messageBus;
        $this->router = $router;
    }

    public function createGameStartedActions(Game $game): void
    {
        $gameActionValues = [
            'gameId' => $game->getId(),
            'type' => $game->getType(),
            'url' => $this->router->generate('api.games.visit', ['id' => $game->getId()]),
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_GAME_STARTED, null, true);
        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_YOUR_GAME_STARTED);
    }

    public function createQuizGamePhaseChosenActions(QuizGame $game, User $user, string $phaseType): void
    {
        $teamPlayer = $game->getTeamPlayerByUser($user);

        if (!$teamPlayer) {
            throw new RuntimeException('User has no team player');
        }

        $gameActionValues = [
            'team' => $teamPlayer->getTeam(),
            'gameId' => $game->getId(),
            'phase' => $phaseType,
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_USER_CHOSE_PHASE_IN_QUIZ, $user);
    }

    public function createQuizGamePlayingActions(QuizGame $game): void
    {
        $gameActionValues = [
            'game' => $game,
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_QUIZ_PLAYING_STARTED);
    }

    public function createUserJoinedToGameAction(Game $game, GameTeam $team, User $user): void
    {
        $gameActionValues = [
            'team' => $team->getId(),
        ];

        $this->createGameAction(
            $game,
            $gameActionValues,
            GameAction::TEMPLATE_USER_JOINED_TO_GAME,
            $user,
            true
        );
    }

    public function createUserLeavedFromGameAction(Game $game, GameTeam $team, User $user): void
    {
        $gameActionValues = [
            'team' => $team->getId(),
        ];

        $this->createGameAction(
            $game,
            $gameActionValues,
            GameAction::TEMPLATE_USER_LEAVED_FROM_GAME,
            $user,
            true
        );
    }

    public function createUserEnteredAnswerAction(
        Game $game,
        GameTeam $team,
        User $user,
        string $userAnswer,
        ?AnswerInterface $answer = null
    ): void {
        $gameActionValues = [
            'team' => $team->getId(),
            'user' => $user->getId(),
            'answer' => $userAnswer,
            'answerId' => $answer ? $answer->getId() : null,
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_USER_FROM_YOUR_TEAM_ENTERED_ANSWER, $user, false, true);

        $gameActionValuesMain = [
            'team' => $team->getId(),
            'user' => $user->getId(),
        ];

        $this->createGameAction($game, $gameActionValuesMain, GameAction::TEMPLATE_QUIZ_GAME_USER_ENTERED_ANSWER, $user);
    }

    public function createGameTurnsChangedAction(Game $game): void
    {
        $gameActionValues = [
            'playerTurns' => $game->getTeamPlayersTurnsIds(),
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_GAME_TURNS_CHANGED);
    }

    public function createNewQuestionInProgressAction(?QuizGame $game): void
    {
        if (!$game) {
            return;
        }

        $gameActionValues = [
            'gameId' => $game->getId(),
            'phase' => $game->getCurrentPhase(),
            'question' => $game->getCurrentQuestion(),
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_QUIZ_NEW_QUESTION_IN_PROGRESS);
    }

    public function createQuizGamePhaseFinishedAction(QuizGame $game, BasePhase $phase): void
    {
        $gameActionValues = [
            'gameId' => $game->getId(),
            'phaseId' => $phase->getId(),
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_QUIZ_PHASE_FINISHED);
    }

    public function createQuizGameFinishedAction(QuizGame $game): void
    {
        $gameActionValues = [
            'gameId' => $game->getId(),
            'winnerTeam' => $game->getTeams()->first()->getId(),
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_QUIZ_GAME_FINISHED);
    }

    private function createGameAction(
        Game $game,
        array $values,
        string $template,
        ?User $user = null,
        bool $sentToAll = false,
        bool $sentToTeam = false
    ): void {
        if (!in_array($template, GameAction::TEMPLATES, true)) {
            throw new AppException(ErrorCode::INCORRECT_GAME_ACTION_TYPE);
        }

        if ($user && !isset($values['userId'])) {
            $values['userId'] = $user->getId();
        }

        if (!isset($values['gameId'])) {
            $values['gameId'] = $game->getId();
        }

        $action = new GameAction();
        $game->addAction($action);
        $action->setJsonValues($values);
        $action->setTemplate($template);
        $action->setUser($user);

        $this->em->persist($action);
        $this->em->flush();

        $emails = [];

        if (!$sentToAll) {
            if ($sentToTeam && $user) {
                $teamPlayer = $game->getTeamPlayerByUser($user);

                if (!$teamPlayer) {
                    throw new RuntimeException('Team player does not exists');
                }

                $emails = $this->em->getRepository(User::class)->findUserEmailsByTeam($teamPlayer->getTeam());
            } else {
                $emails = $this->em->getRepository(User::class)->findUserEmailsByGame($game);
            }
        }

        $amqpGameAction = new AmqpGameAction($action, $emails, $sentToAll);

        $this->messageBus->dispatch(
            new Envelope(
                $amqpGameAction,
                [
                    new SerializerStamp(['groups' => 'AMQP']),
                ]
            )
        );
    }

    public function createGamePointsChangedAction(?Game $game): void
    {
        if (!$game) {
            return;
        }

        $gameActionValues = [
            'teams' => $game->getTeams(),
        ];

        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_GAME_POINTS_CHANGED);
    }
}
