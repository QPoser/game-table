<?php
declare(strict_types=1);

namespace App\Services\Game;

use App\AmqpMessages\AmqpGameAction;
use App\Entity\Game\Game;
use App\Entity\Game\GameAction;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Response\ErrorCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;

class GameActionService
{
    private EntityManagerInterface $em;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $messageBus)
    {
        $this->em = $em;
        $this->messageBus = $messageBus;
    }

    public function createGameAction(
        Game $game,
        array $values,
        string $template,
        ?User $user = null,
        bool $sentToAll = false
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
            $emails = $this->em->getRepository(User::class)->findUserEmailsByGame($game);
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