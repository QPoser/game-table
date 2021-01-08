<?php

declare(strict_types=1);

namespace App\AmqpMessages;

use App\Entity\Game\GameAction;
use Symfony\Component\Serializer\Annotation\Groups;

final class AmqpGameAction extends BaseAmqpMessage
{
    /**
     * @Groups({"AMQP"})
     */
    private ?GameAction $action = null;

    public function __construct(GameAction $action, array $emails, bool $sentToAll = false)
    {
        $this->action = $action;
        parent::__construct($emails, $sentToAll);
    }

    public function getAction(): ?GameAction
    {
        return $this->action;
    }
}
