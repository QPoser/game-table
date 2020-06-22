<?php
declare(strict_types=1);

namespace App\Entity\Core;

use App\Entity\User;
use App\Repository\Core\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    public const TYPE_PUSH = 'push';

    public const TEMPLATE_GAME_CREATED = 'game_created';
    public const TEMPLATE_GAME_STARTED = 'game_started';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private ?User $user;

    /**
     * @ORM\Column(type="json")
     * @Groups({"AMQP"})
     */
    private array $jsonValues = [];

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups({"Exclude"})
     */
    private ?string $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"AMQP"})
     */
    private ?string $template;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getJsonValues(): array
    {
        return $this->jsonValues;
    }

    public function setJsonValues(array $jsonValues): self
    {
        $this->jsonValues = $jsonValues;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }
}
