<?php
declare(strict_types=1);

namespace App\Services\Game;

use App\AmqpMessages\AmqpGameAction;
use App\Entity\Game\Game;
use App\Entity\Game\GameAction;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Response\ErrorCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Routing\RouterInterface;

class GameActionService
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
        $this->createGameAction($game, $gameActionValues, GameAction::TEMPLATE_YOUR_GAME_STARTED, null);
    }

    public function createQuizGamePhaseChosenActions(QuizGame $game, User $user): void
    {
        $gameActionValues = [
            'team' => $game->getTeamPlayerByUser($user)->getTeam(),
            'gameId' => $game->getId(),
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

    private function createGameAction(
        Game $game,
        array $values,
        string $template,
        ?User $user = null,
        bool $sentToAll = false,
        bool $sentToTeam = false
    ): void
    {
        if (!in_array($template, GameAction::TEMPLATES, true)) {
            throw new AppException(ErrorCode::INCORRECT_GAME_ACTION_TYPE);
        }

        if ($user && !isset($values['userId'])) {
            $values['userId'] = $user->getId();
        }

        $action = new GameAction();
        $action->setGame($game);
        $action->setJsonValues($values);
        $action->setTemplate($template);
        $action->setUser($user);

        $this->em->persist($action);
        $this->em->flush($action);

        $emails = [];

        if (!$sentToAll) {
            if ($sentToTeam && $user) {
                $emails = $this->em->getRepository(User::class)->findUserEmailsByTeam($game->getTeamPlayerByUser($user)->getTeam());
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
}