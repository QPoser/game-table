<?php
declare(strict_types=1);

namespace App\Entity\Game;

use App\Entity\Game\Chat\Message;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\RoomRepository")
 */
class Room
{
    const MAX_SLOTS = 16;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="3", max="32",
     *     minMessage="Room title must be at least {{ limit }} characters long",
     *     maxMessage="Room title cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"Minimal", "Api"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="1", max="16", notInRangeMessage="Slots value should be between {{ min }} and {{ max }}")
     * @Groups({"Minimal", "Api"})
     */
    private ?int $slots;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"Minimal", "Api"})
     */
    private ?string $rules;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Assert\Length(
     *     min="1", max="32", allowEmptyString=true,
     *     minMessage="Room password must be at least {{ limit }} characters long",
     *     maxMessage="Room password cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"Exclude"})
     */
    private ?string $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game\RoomPlayer", mappedBy="room", orphanRemoval=true)
     * @Groups({"Exclude"})
     */
    private Collection $roomPlayers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game\Chat\Message", mappedBy="room", orphanRemoval=true)
     * @Groups({"Exclude"})
     */
    private $messages;

    public function __construct()
    {
        $this->title = null;
        $this->rules = null;
        $this->password = null;
        $this->slots = self::MAX_SLOTS;
        $this->roomPlayers = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlots(): ?int
    {
        return $this->slots;
    }

    public function setSlots(int $slots): self
    {
        $this->slots = $slots;

        return $this;
    }

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(?string $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoomPlayers(): Collection
    {
        return $this->roomPlayers;
    }

    public function addRoomPlayer(RoomPlayer $roomPlayer): self
    {
        if (!$this->roomPlayers->contains($roomPlayer)) {
            $this->roomPlayers[] = $roomPlayer;
            $roomPlayer->setRoom($this);
        }

        return $this;
    }

    public function removeRoomPlayer(RoomPlayer $roomPlayer): self
    {
        if ($this->roomPlayers->contains($roomPlayer)) {
            $this->roomPlayers->removeElement($roomPlayer);
            // set the owning side to null (unless already changed)
            if ($roomPlayer->getRoom() === $this) {
                $roomPlayer->setRoom(null);
            }
        }

        return $this;
    }

    public function getRoomPlayersCount(): int
    {
        return $this->roomPlayers->filter(fn($player) => RoomPlayer::isActiveRoomPlayer($player))->count();
    }

    public function hasSlots(): bool
    {
        return $this->getRoomPlayersCount() < $this->slots;
    }

    public function hasUser(?User $user, bool $checkActive = false): bool
    {
        if ($user) {
            foreach ($this->roomPlayers as $roomPlayer) {
                /** @var RoomPlayer $roomPlayer */
                if ($roomPlayer->getPlayer()->getId() === $user->getId()) {
                    return !$checkActive || RoomPlayer::isActiveRoomPlayer($roomPlayer);
                }
            }
        }

        return false;
    }

    public function getRoomPlayerByUser(User $user): ?RoomPlayer
    {
        foreach ($this->roomPlayers as $roomPlayer) {
            /** @var RoomPlayer $roomPlayer */
            if ($roomPlayer->getPlayer()->getId() === $user->getId()) {
                return $roomPlayer;
            }
        }

        return null;
    }

    public function canUserJoin(User $user): bool
    {
        $roomPlayer = $this->getRoomPlayerByUser($user);

        if ((!$roomPlayer || $roomPlayer->getStatus() === RoomPlayer::STATUS_LEAVED) && $this->hasSlots()) {
            return true;
        }

        return false;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setRoom($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getRoom() === $this) {
                $message->setRoom(null);
            }
        }

        return $this;
    }
}
