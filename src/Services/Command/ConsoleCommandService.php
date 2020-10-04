<?php
declare(strict_types=1);

namespace App\Services\Command;

use App\Entity\Command\Command;
use App\Message\ConsoleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Process\Process;

class ConsoleCommandService
{
    private const COMMAND_PREFIX = 'php bin/console ';

    private MessageBusInterface $bus;

    private EntityManagerInterface $em;

    public function __construct(MessageBusInterface $bus, EntityManagerInterface $em)
    {
        $this->bus = $bus;
        $this->em = $em;
    }

    public function addCommandToQueueWithDelay20S(string $command): void
    {
        $consoleCommand = new Command();
        $consoleCommand->setCommand($command);
        $consoleCommand->setUid(uniqid('', true));

        $this->em->persist($consoleCommand);
        $this->em->flush($consoleCommand);

        $this->bus->dispatch(new Envelope(new ConsoleCommand($command, $consoleCommand->getUid()), [
            new DelayStamp(30500),
        ]));
    }

    public function executeCommandByUid(string $uid): void
    {
        /** @var Command $command */
        $command = $this->em->getRepository(Command::class)->findOneBy(['uid' => $uid]);

        if (!$command) {
            return;
        }

        $process = Process::fromShellCommandline(self::COMMAND_PREFIX . $command->getCommand());
        $process->setTimeout(60);
        $process->setIdleTimeout(null);

        $process->start();
        $process->wait();

        if ($process->isSuccessful()) {
            $command->setStatus(Command::STATUS_FINISHED);
        } else {
            $command->setStatus(Command::STATUS_FAILED);
            $command->setTries($command->getTries() + 1);
        }

        $this->em->flush($command);
    }
}