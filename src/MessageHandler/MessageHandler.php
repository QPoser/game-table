<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Game\Chat\Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MessageHandler implements MessageHandlerInterface
{
    public function __invoke(Message $message): void
    {
        // TODO: Implement __invoke() method.
    }
}