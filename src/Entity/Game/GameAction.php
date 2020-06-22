<?php
declare(strict_types=1);

namespace App\Entity\Game;

use App\Entity\User;
use App\Repository\Game\GameActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameActionRepository::class)
 */
class GameAction
{
    public const TEMPLATE_GAME_STARTED = 'game_started';

    public const TEMPLATES = [
          self::TEMPLATE_GAME_STARTED => self::TEMPLATE_GAME_STARTED,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="json")
     * @Groups({"AMQP"})
     */
    private array $jsonValues = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @Groups({"AMQP"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"AMQP"})
     */
    private ?Game $game;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"AMQP"})
     */
    private ?string $template;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJsonValues(): ?array
    {
        return $this->jsonValues;
    }

    public function setJsonValues(array $jsonValues): self
    {
        $this->jsonValues = $jsonValues;

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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        if (in_array($template, self::TEMPLATES, true)) {
            $this->template = $template;
        }

        return $this;
    }
}