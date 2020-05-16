<?php
declare(strict_types=1);

namespace App\Entity\Game\Chat;

use App\Entity\Game\Room;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\Chat\MessageRepository")
 */
class Message
{
    const TYPE_ROOM = 'room';
    const TYPE_PRIVATE = 'private';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game\Room", inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?Room $room;

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
    private ?string $type = self::TYPE_ROOM;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
