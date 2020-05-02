<?php
declare(strict_types=1);

namespace App\Services\Chat;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;

class MessageService
{
    private EntityManagerInterface $em;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $messageBus)
    {
        $this->em = $em;
        $this->messageBus = $messageBus;
    }

    public function createMessage(Room $room, User $user, string $content): Message
    {
        $message = new Message();
        $message->setRoom($room);
        $message->setUser($user);
        $message->setContent($content);

        $this->em->persist($message);
        $this->em->flush($message);

        $this->messageBus->dispatch(
            new Envelope(
                $message,
                [
                    new SerializerStamp(['groups' => 'AMPQ']),
                ]
            )
        );

        return $message;
    }
}