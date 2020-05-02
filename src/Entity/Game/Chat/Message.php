<?php
declare(strict_types=1);

namespace App\Entity\Game\Chat;

use App\Entity\Game\Room;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ex;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Game\Chat\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Minimal", "Api", "AMPQ"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game\Room", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Minimal", "Api", "AMPQ"})
     */
    private ?Room $room;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Minimal", "Api", "AMPQ"})
     */
    private ?User $user;

    /**
     * @ORM\Column(type="text")
     * @Groups({"Minimal", "Api", "AMPQ"})
     */
    private ?string $content = null;

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
}
