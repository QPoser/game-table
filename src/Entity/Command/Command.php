<?php

declare(strict_types=1);

namespace App\Entity\Command;

use App\Repository\Command\CommandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    public const STATUS_CREATED = 'created';
    public const STATUS_FAILED = 'failed';
    public const STATUS_FINISHED = 'finished';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $command;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $uid;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private string $status = self::STATUS_CREATED;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $tries = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTries(): int
    {
        return $this->tries;
    }

    public function setTries(int $tries): self
    {
        $this->tries = $tries;

        return $this;
    }
}
