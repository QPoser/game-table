<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

use App\Entity\Game\Quiz\QuizGame;
use App\Repository\Game\Quiz\Phase\BasePhaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass=BasePhaseRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      BasePhase::TYPE_QUESTIONS = "App\Entity\Game\Quiz\Phase\Questions\QuestionsPhase"
 * })
 */
abstract class BasePhase
{
    public const TYPE_QUESTIONS = 'questions';
    public const TYPE_PRICES = 'prices';
    public const TYPE_MUSIC_REVERSE = 'music_reverse';
    public const TYPE_MOVIES = 'movies';
    public const TYPE_BRANDS = 'brands';
    public const TYPE_ASSOCIATIONS = 'associations';

    public const AVAILABLE_TYPES = [
        self::TYPE_QUESTIONS => self::TYPE_QUESTIONS,
    ];

    public const STATUS_PREPARED = 'created';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FINISHED = 'finished';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $vip;

    /**
     * @ORM\ManyToOne(targetEntity=QuizGame::class, inversedBy="phases")
     * @ORM\JoinColumn(nullable=false)
     */
    private QuizGame $game;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Groups({"Api", "AMQP"})
     */
    private string $status = self::STATUS_PREPARED;

    /**
     * @Groups({"Api"})
     */
    abstract public function getType(): string;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVip(): ?bool
    {
        return $this->vip;
    }

    public function setVip(?bool $vip): self
    {
        $this->vip = $vip;

        return $this;
    }

    public function getGame(): ?QuizGame
    {
        return $this->game;
    }

    public function setGame(?QuizGame $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
