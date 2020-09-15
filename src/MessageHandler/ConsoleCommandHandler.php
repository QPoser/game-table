<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ConsoleCommand;
use App\Services\Command\ConsoleCommandService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ConsoleCommandHandler implements MessageHandlerInterface
{
    private ConsoleCommandService $consoleCommandService;

    public function __construct(ConsoleCommandService $consoleCommandService)
    {
        $this->consoleCommandService = $consoleCommandService;
    }

    public function __invoke(ConsoleCommand $command)
    {
        $this->consoleCommandService->executeCommandByUid($command->getUid());
    }
}