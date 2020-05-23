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

    public function __construct(array $emails)
    {
        $this->emails = $emails;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }
}