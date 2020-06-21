<?php
declare(strict_types=1);

namespace App\Services\Notification;

use App\AmqpMessages\AmqpChatMessage;
use App\AmqpMessages\AmqpNotification;
use App\Entity\Core\Notification;
use App\Entity\Game\Game;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;

class GameNotificationTemplateHelper
{
    private EntityManagerInterface $em;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $messageBus)
    {
        $this->em = $em;
        $this->messageBus = $messageBus;
    }

    public function createGameCreatedNotifications(User $creator, Game $game): void
    {
        $notification = new Notification();

        $notification->setType(Notification::TYPE_PUSH);
        $notification->setTemplate(Notification::TEMPLATE_GAME_CREATED);
        $notification->setJsonValues(['game' => $game->getTitle()]);
        $notification->setUser($creator);

        $this->em->persist($notification);
        $this->em->flush();

        $amqpNotification = new AmqpNotification($notification, [$creator->getEmail()]);

        $this->messageBus->dispatch(
            new Envelope(
                $amqpNotification,
                [
                    new SerializerStamp(['groups' => 'AMQP']),
                ]
            )
        );
    }
}