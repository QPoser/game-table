<?php
declare(strict_types=1);

namespace App\Message;

class ConsoleCommand
{
    private string $command;
    private string $uid;

    public function __construct(string $command, string $uid)
    {
        $this->command = $command;
        $this->uid = $uid;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getUid(): string
    {
        return $this->uid;
    }
}