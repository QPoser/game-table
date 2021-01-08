<?php

declare(strict_types=1);

namespace App\Services\Chat;

use App\AmqpMessages\AmqpChatMessage;
use App\Entity\Game\Chat\Message;
use App\Entity\Game\Game;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Response\ErrorCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;

final class ChatService
{
    private EntityManagerInterface $em;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $messageBus)
    {
        $this->em = $em;
        $this->messageBus = $messageBus;
    }

    public function createMessage(Game $game, User $user, string $content, string $type): Message
    {
        if (!in_array($type, Message::TYPES, true)) {
            throw new AppException(ErrorCode::INCORRECT_MESSAGE_TYPE);
        }

        $message = new Message();
        $message->setGame($game);
        $message->setUser($user);
        $message->setContent($content);
        $message->setType($type);

        $emails = [];

        if ($type === Message::TYPE_TEAM) {
            /** @var GameTeam $team */
            $team = $game->getTeamPlayerByUser($user)->getTeam();
            $message->setTeam($team);

            $emails = $this->em->getRepository(User::class)->findUserEmailsByTeam($team);
        } elseif ($type === Message::TYPE_GAME) {
            $emails = $this->em->getRepository(User::class)->findUserEmailsByGame($game);
        }

        $this->em->persist($message);
        $this->em->flush($message);

        $amqpMessage = new AmqpChatMessage($message, $emails);

        $this->messageBus->dispatch(
            new Envelope(
                $amqpMessage,
                [
                    new SerializerStamp(['groups' => 'AMQP']),
                ]
            )
        );

        return $message;
    }
}
