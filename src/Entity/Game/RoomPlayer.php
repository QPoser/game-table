<?php
declare(strict_types=1);

namespace App\Entity\Game;

use App\Entity\Traits\TimeStampTrait;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\RoomPlayerRepository")
 * @UniqueEntity(fields={"room", "player"})
 */
class RoomPlayer
{
    const STATUS_MASTER = 'master';
    const STATUS_PLAYER = 'player';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_LEAVED = 'leaved';

    const ACTIVE_STATUSES = [
        self::STATUS_MASTER,
        self::STATUS_PLAYER,
    ];

    use TimeStampTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game\Room", inversedBy="roomPlayers")
     * @ORM\JoinColumn(nullable=false)
     */
    private Room $room;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private ?string $status;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $role;

    public static function isActiveRoomPlayer(self $roomPlayer): bool
    {
        return in_array($roomPlayer->getStatus(), self::ACTIVE_STATUSES);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
