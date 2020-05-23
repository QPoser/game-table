<?php
declare(strict_types=1);

namespace App\AmqpMessages;

use App\Entity\Game\Chat\Message;
use Symfony\Component\Serializer\Annotation\Groups;

class AmqpChatMessage extends BaseAmqpMessage
{
    /**
     * @Groups({"AMQP"})
     */
    private ?Message $message = null;

    public function __construct(Message $message, array $emails)
    {
        $this->message = $message;
        parent::__construct($emails);
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }
}