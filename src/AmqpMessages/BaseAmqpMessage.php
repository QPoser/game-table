<?php

declare(strict_types=1);

namespace App\AmqpMessages;

use Symfony\Component\Serializer\Annotation\Groups;

abstract class BaseAmqpMessage
{
    /**
     * @Groups({"AMQP"})
     */
    private array $emails;

    /**
     * @Groups({"AMQP"})
     */
    private bool $sentToAll;

    public function __construct(array $emails, bool $sentToAll = false)
    {
        $this->emails = $emails;
        $this->sentToAll = $sentToAll;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function isSentToAll(): bool
    {
        return $this->sentToAll;
    }
}
