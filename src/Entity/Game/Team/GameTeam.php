<?php
declare(strict_types=1);

namespace App\Entity\Game\Team;

use App\Entity\Game\Game;
use App\Entity\User;
use App\Repository\Game\Team\GameTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameTeamRepository::class)
 */
class GameTeam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Api", "AMQP"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Excluded"})
     */
    private ?Game $game;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Api", "AMQP"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"Api", "AMQP"})
     */
    private ?int $slots;

    /**
     * @ORM\OneToMany(targetEntity=GameTeamPlayer::class, mappedBy="team", orphanRemoval=true)
     * @Groups({"Api", "AMQP"})
     */
    private Collection $players;

    /**
     * @Groups({"Api"})
     */
    private bool $userInTeam = false;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(GameTeamPlayer $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(GameTeamPlayer $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }

    public function getSlots(): ?int
    {
        return $this->slots;
    }

    public function setSlots(int $slots): self
    {
        $this->slots = $slots;

        return $this;
    }

    public function hasUser(User $user): bool
    {
        foreach ($this->players as $teamPlayer) {
            /** @var GameTeamPlayer $teamPlayer */
            if ($teamPlayer->getUser()->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getPlayersCount(): int
    {
        return $this->players->count();
    }

    public function getPlayerByUser(User $user): ?GameTeamPlayer
    {
        foreach ($this->players as $teamPlayer) {
            /** @var GameTeamPlayer $teamPlayer */
            if ($teamPlayer->getUser()->getId() === $user->getId()) {
                return $teamPlayer;
            }
        }

        return null;
    }

    public function hasSlot(): bool
    {
        return $this->slots > $this->players->count();
    }

    public function isUserInTeam(): bool
    {
        return $this->userInTeam;
    }

    public function setUserInTeam(bool $userInTeam): self
    {
        $this->userInTeam = $userInTeam;

        return $this;
    }

    public function getPlayerWithTurn(): ?GameTeamPlayer
    {
        foreach ($this->players as $player) {
            /** @var GameTeamPlayer $player */
            if ($player->isPlayerTurn()) {
                return $player;
            }
        }

        return null;
    }

    public function getUsersIds(): array
    {
        $ids = [];

        foreach ($this->players as $player) {
            /** @var GameTeamPlayer $player */
            $ids[] = $player->getUser()->getId();
        }

        return $ids;
    }
}
