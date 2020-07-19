<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

use App\Entity\Game\Quiz\QuizGame;
use App\Repository\Game\Quiz\Phase\BasePhaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\User;

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
    public const TYPE_QUESTIONS = 'questions'; // Base questions with 4 answers
    public const TYPE_PRICES = 'prices'; // Base question with one int answer from user
    public const TYPE_MUSIC_REVERSE = 'music_reverse'; // Question with music, answer - string from user, we will match song
    public const TYPE_MOVIES = 'movies'; // Question with picture from film, answer - string from user, we will match name
    public const TYPE_BRANDS = 'brands'; // Question with picture, we will return letters, answer from user - string constructed from letters.
    public const TYPE_ASSOCIATIONS = 'associations'; // Question with some icon-pictures, they will show in 5-10sec in queue, answer - string from user, we will match word.

    public const AVAILABLE_TYPES = [
        self::TYPE_QUESTIONS => self::TYPE_QUESTIONS,
    ];

    public const FREE_TYPES = [
        self::TYPE_QUESTIONS => self::TYPE_QUESTIONS,
    ];

    public const VIP_TYPES = [
        self::TYPE_PRICES => self::TYPE_PRICES,
    ];

    public const STATUS_PREPARED = 'prepared';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FINISHED = 'finished';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Api", "AMQP"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=QuizGame::class, inversedBy="phases")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private ?QuizGame $game;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Groups({"Api", "AMQP"})
     */
    private string $status = self::STATUS_PREPARED;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Api", "AMQP"})
     */
    private ?User $user = null;

    /**
     * @Groups({"Api", "AMQP"})
     */
    abstract public function getType(): string;

    abstract public function getCurrentQuestion(): ?QuestionInterface;

    /**
     * @Groups({"Api", "AMQP"})
     */
    abstract public function isFreeAnswer(): bool; // Can user answer be unmatched with question answers?

    /**
     * @Groups({"Api", "AMQP"})
     */
    abstract public function isAllQuestionsFinished(): bool;

    abstract public function getCurrentPhaseQuestion(): ?PhaseQuestionInterface;

    abstract public function isLastQuestion(): bool;

    abstract public function closeQuestion(): void;

    public function getId(): ?int
    {
        return $this->id;
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

    public function play(): void
    {
        $this->setStatus(self::STATUS_IN_PROGRESS);
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public static function getFormattedTypes(): array
    {
        return [
            'free' => self::FREE_TYPES,
            'vip' => self::VIP_TYPES,
        ];
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
}
