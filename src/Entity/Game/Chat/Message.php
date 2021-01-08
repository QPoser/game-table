<?php

declare(strict_types=1);

namespace App\Entity\Game\Chat;

use App\Entity\Game\Game;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\Chat\MessageRepository")
 */
class Message
{
    public const TYPE_GAME = 'game';
    public const TYPE_TEAM = 'team';
    public const TYPE_PRIVATE = 'private';

    public const TYPES = [
        self::TYPE_GAME => self::TYPE_GAME,
        self::TYPE_TEAM => self::TYPE_TEAM,
        self::TYPE_PRIVATE => self::TYPE_PRIVATE,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game\Game", inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?Game $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?User $user;

    /**
     * @ORM\Column(type="text")
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="text")
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?string $type = self::TYPE_GAME;

    /**
     * @ORM\ManyToOne(targetEntity=GameTeam::class)
     * @ORM\JoinColumn(fieldName="team_id", referencedColumnName="id", nullable=true)
     * @Groups({"AMQP"})
     */
    private ?GameTeam $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        if (in_array($type, self::TYPES, true)) {
            $this->type = $type;
        }

        return $this;
    }

    public function getTeam(): ?GameTeam
    {
        return $this->team;
    }

    public function setTeam(?GameTeam $team): self
    {
        $this->team = $team;

        return $this;
    }
}
