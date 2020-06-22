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
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api"})
     */
    private ?User $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"Excluded"})
     */
    private ?string $role = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Api"})
     */
    private bool $hiddenRole = true;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Api"})
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function isPlayerTurn(): ?bool
    {
        return $this->playerTurn;
    }

    public function setPlayerTurn(bool $playerTurn): self
    {
        $this->playerTurn = $playerTurn;

        return $this;
    }

    public function isHiddenRole(): ?bool
    {
        return $this->hiddenRole;
    }

    public function setHiddenRole(bool $hiddenRole): self
    {
        $this->hiddenRole = $hiddenRole;

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

    /**
     * @Groups({"Api"})
     */
    public function getApiRole(): ?string
    {
        return $this->hiddenRole ? $this->role : null;
    }
}
