<?php
declare(strict_types=1);

namespace App\AmqpMessages;

use App\Entity\Game\Chat\Message;
use Symfony\Component\Serializer\Annotation\Groups;

class AmqpChatMessage
{
    /**
     * @Groups({"AMQP"})
     */
    private ?Message $message = null;

    /**
     * @Groups({"AMQP"})
     */
    private array $emails;

    public function __construct(Message $message, array $emails)
    {
        $this->message = $message;
        $this->emails = $emails;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }
}