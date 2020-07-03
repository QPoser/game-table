<?php
declare(strict_types=1);

namespace App\Entity\Game;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Team\GameTeam;
use App\Entity\Game\Team\GameTeamPlayer;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="App\Repository\Game\GameRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      Game::TYPE_QUIZ = "App\Entity\Game\Quiz\QuizGame"
 * })
 */
abstract class Game
{
    public const DEFAULT_SLOTS_IN_TEAM = 1;
    public const MAX_TEAMS = 2;
    public const STRICT_TEAMS = false;

    public const TYPE_QUIZ = 'quiz';

    public const ACCESS_PUBLIC = 'public';
    public const ACCESS_PRIVATE = 'private';

    public const STATUS_CREATED = 'created';
    public const STATUS_STARTED = 'started';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CLOSED = 'closed';

    public const ACCESS_TYPES = [
        self::ACCESS_PUBLIC => self::ACCESS_PUBLIC,
        self::ACCESS_PRIVATE => self::ACCESS_PRIVATE,
    ];

    public const STATUSES = [
        self::STATUS_CREATED => self::STATUS_CREATED,
        self::STATUS_STARTED => self::STATUS_STARTED,
        self::STATUS_FINISHED => self::STATUS_FINISHED,
        self::STATUS_CLOSED => self::STATUS_CLOSED,
    ];

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
     *     minMessage="Game title must be at least {{ limit }} characters long",
     *     maxMessage="Game title cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"Minimal", "Api", "AMQP"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Assert\Length(
     *     min="1", max="32", allowEmptyString=true,
     *     minMessage="Game password must be at least {{ limit }} characters long",
     *     maxMessage="Game password cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"Exclude"})
     */
    private ?string $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game\Chat\Message", mappedBy="game", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     * @Groups({"GameMessages"})
     */
    private Collection $messages;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Groups({"GameAccess"})
     */
    private ?string $access;

    /**
     * @ORM\OneToMany(targetEntity=GameTeam::class, mappedBy="game", orphanRemoval=true)
     * @Groups({"Api", "AMQP"})
     */
    private Collection $teams;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Api", "AMQP"})
     */
    private bool $autoCreated;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * @Groups({"Api", "AMQP"})
     */
    private ?User $creator;

    /**
     * @Groups({"Api"})
     */
    private bool $userInGame = false;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Groups({"Api", "AMQP"})
     */
    private string $status = self::STATUS_CREATED;

    /**
     * @ORM\OneToMany(targetEntity=GameAction::class, mappedBy="game", orphanRemoval=true)
     */
    private Collection $actions;

    /**
     * @Groups({"Api"})
     */
    abstract public function getType(): string;

    public function __construct()
    {
        $this->title = null;
        $this->password = null;
        $this->messages = new ArrayCollection();
        $this->access = self::ACCESS_PUBLIC;
        $this->teams = new ArrayCollection();
        $this->autoCreated = true;
        $this->actions = new ArrayCollection();
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setGame($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getGame() === $this) {
                $message->setGame(null);
            }
        }

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->access === self::ACCESS_PUBLIC;
    }

    public function isPrivate(): bool
    {
        return $this->access === self::ACCESS_PRIVATE;
    }

    public function getAccess(): ?string
    {
        return $this->access;
    }

    public function setAccess(?string $access): self
    {
        if (in_array($access, self::ACCESS_TYPES, true)) {
            $this->access = $access;
        }

        return $this;
    }

    /**
     * @Groups({"Api"})
     */
    public function isSecure(): bool
    {
        return $this->getPassword() ? true : false;
    }

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(GameTeam $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setGame($this);
        }

        return $this;
    }

    public function removeTeam(GameTeam $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getGame() === $this) {
                $team->setGame(null);
            }
        }

        return $this;
    }

    public function isAutoCreated(): bool
    {
        return $this->autoCreated;
    }

    public function setAutoCreated(bool $autoCreated): Game
    {
        $this->autoCreated = $autoCreated;

        return $this;
    }

    public function getTeamPlayerByUser(User $user): ?GameTeamPlayer
    {
        foreach ($this->teams as $team) {
            /** @var GameTeam $team */
            if ($teamPlayer = $team->getPlayerByUser($user)) {
                return $teamPlayer;
            }
        }

        return null;
    }

    public function hasUser(User $user): bool
    {
        foreach ($this->teams as $team) {
            /** @var GameTeam $team */
            if ($team->hasUser($user)) {
                return true;
            }
        }

        return false;
    }

    public function getTeamById(int $id): ?GameTeam
    {
        foreach ($this->teams as $team) {
            if ($team->getId() === $id) {
                return $team;
            }
        }

        return null;
    }

    public function canUserJoin(User $user): bool
    {
//        if ($this->hasUser($user)) {
//            return false;
//        }

        if ($this->isFull()) {
            return false;
        }

        return true;
    }

    public function getTeamWithSlot(): ?GameTeam
    {
        foreach ($this->teams as $team) {
            if ($team->hasSlot()) {
                return $team;
            }
        }

        return null;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function isUserInGame(): bool
    {
        return $this->userInGame;
    }

    public function setUserInGame(bool $userInGame): self
    {
        $this->userInGame = $userInGame;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (in_array($status, self::STATUSES, true)) {
            $this->status = $status;
        }

        return $this;
    }

    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(GameAction $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->setGame($this);
        }

        return $this;
    }

    public function removeAction(GameAction $action): self
    {
        if ($this->actions->contains($action)) {
            $this->actions->removeElement($action);
            // set the owning side to null (unless already changed)
            if ($action->getGame() === $this) {
                $action->setGame(null);
            }
        }

        return $this;
    }

    public function isFull(): bool
    {
        foreach ($this->teams as $team) {
            if ($team->hasSlot()) {
                return false;
            }
        }

        return true;
    }

    public function isUserTurn(User $user): bool
    {
        $player = $this->getTeamPlayerByUser($user);

        if (!$player) {
            return false;
        }

        return $player->isPlayerTurn();
    }

    public function disablePlayersTurns(): void
    {
        foreach ($this->teams as $team) {
            /** @var GameTeam $team */

            foreach ($team->getPlayers() as $player) {
                /** @var GameTeamPlayer $player */
                $player->setPlayerTurn(false);
            }
        }
    }

    public function setPlayersTurnInEveryTeam(int $playerIndex): void
    {
        $this->disablePlayersTurns();

        foreach ($this->teams as $team) {
            /** @var GameTeam $team */

            $player = $team->getPlayers()->get($playerIndex);

            if ($player) {
                $player->setPlayerTurn(false);
            }
        }
    }

    public function disablePlayerTurnByUser(User $user): void
    {
        $player = $this->getTeamPlayerByUser($user);

        if ($player) {
            $player->setPlayerTurn(false);
        }
    }

    private function setTeamPlayerTurnByIndex(int $teamIndex): void
    {
        $this->disablePlayersTurns();

        /** @var GameTeam $team */
        $team = $this->teams->get($teamIndex);

        /** @var GameTeamPlayer $player */
        $player = $team->getPlayers()->first();

        $player->setPlayerTurn(true);
    }

    public function setFirstTeamPlayerTurn(): void
    {
        $this->setTeamPlayerTurnByIndex(0);
    }

    public function setSecondTeamPlayerTurn(): void
    {
        $this->setTeamPlayerTurnByIndex(1);
    }
}
