<?php
declare(strict_types=1);

namespace App\AmqpMessages;

use App\Entity\Core\Notification;
use Symfony\Component\Serializer\Annotation\Groups;

class AmqpNotification extends BaseAmqpMessage
{
    /**
     * @Groups({"AMQP"})
     */
    private ?Notification $notification = null;

    public function __construct(Notification $notification, array $emails)
    {
        $this->notification = $notification;
        parent::__construct($emails);
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }
}