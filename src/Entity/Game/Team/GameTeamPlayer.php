<?php

declare(strict_types=1);

namespace App\Entity\Game\Team;

use App\Entity\User;
use App\Repository\Game\Team\GameTeamPlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameTeamPlayerRepository::class)
 */
class GameTeamPlayer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Api", "AMQP"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private ?User $user;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Api", "AMQP"})
     */
    private bool $playerTurn = true;

    /**
     * @ORM\ManyToOne(targetEntity=GameTeam::class, inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Excluded"})
     */
    private ?GameTeam $team;

    public function getId(): ?int
    {
        return $this->id;
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

    public function isPlayerTurn(): bool
    {
        return $this->playerTurn;
    }

    public function setPlayerTurn(bool $playerTurn): self
    {
        $this->playerTurn = $playerTurn;

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
