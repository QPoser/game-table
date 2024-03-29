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
    public const TEMPLATE_YOUR_GAME_STARTED = 'your_game_started';
    public const TEMPLATE_USER_JOINED_TO_GAME = 'user_joined_to_game';
    public const TEMPLATE_USER_LEAVED_FROM_GAME = 'user_leaved_from_game';
    public const TEMPLATE_USER_CHOSE_PHASE_IN_QUIZ = 'user_chose_phase_in_quiz';
    public const TEMPLATE_QUIZ_PLAYING_STARTED = 'quiz_playing_started';
    public const TEMPLATE_QUIZ_GAME_USER_ENTERED_ANSWER = 'quiz_game_user_entered_answer';
    public const TEMPLATE_USER_FROM_YOUR_TEAM_ENTERED_ANSWER = 'user_from_your_team_entered_answer';
    public const TEMPLATE_GAME_TURNS_CHANGED = 'game_turns_changed';
    public const TEMPLATE_QUIZ_NEW_QUESTION_IN_PROGRESS = 'quiz_new_question_in_progress';
    public const TEMPLATE_QUIZ_PHASE_FINISHED = 'quiz_phase_finished';
    public const TEMPLATE_QUIZ_GAME_FINISHED = 'quiz_game_finished';
    public const TEMPLATE_GAME_POINTS_CHANGED = 'game_points_changed';

    public const TEMPLATES = [
          self::TEMPLATE_GAME_STARTED => self::TEMPLATE_GAME_STARTED,
          self::TEMPLATE_YOUR_GAME_STARTED => self::TEMPLATE_YOUR_GAME_STARTED,
          self::TEMPLATE_USER_JOINED_TO_GAME => self::TEMPLATE_USER_JOINED_TO_GAME,
          self::TEMPLATE_USER_LEAVED_FROM_GAME => self::TEMPLATE_USER_LEAVED_FROM_GAME,
          self::TEMPLATE_USER_CHOSE_PHASE_IN_QUIZ => self::TEMPLATE_USER_CHOSE_PHASE_IN_QUIZ,
          self::TEMPLATE_QUIZ_PLAYING_STARTED => self::TEMPLATE_QUIZ_PLAYING_STARTED,
          self::TEMPLATE_QUIZ_GAME_USER_ENTERED_ANSWER => self::TEMPLATE_QUIZ_GAME_USER_ENTERED_ANSWER,
          self::TEMPLATE_USER_FROM_YOUR_TEAM_ENTERED_ANSWER => self::TEMPLATE_USER_FROM_YOUR_TEAM_ENTERED_ANSWER,
          self::TEMPLATE_GAME_TURNS_CHANGED => self::TEMPLATE_GAME_TURNS_CHANGED,
          self::TEMPLATE_QUIZ_NEW_QUESTION_IN_PROGRESS => self::TEMPLATE_QUIZ_NEW_QUESTION_IN_PROGRESS,
          self::TEMPLATE_QUIZ_PHASE_FINISHED => self::TEMPLATE_QUIZ_PHASE_FINISHED,
          self::TEMPLATE_QUIZ_GAME_FINISHED => self::TEMPLATE_QUIZ_GAME_FINISHED,
          self::TEMPLATE_GAME_POINTS_CHANGED => self::TEMPLATE_GAME_POINTS_CHANGED,
    ];

    public const USER_TURN_TEMPLATES = [
        self::TEMPLATE_QUIZ_GAME_USER_ENTERED_ANSWER => self::TEMPLATE_QUIZ_GAME_USER_ENTERED_ANSWER,
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
