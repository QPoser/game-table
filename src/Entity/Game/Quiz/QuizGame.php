<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\QuestionInterface;
use App\Exception\AppException;
use App\Repository\Game\Quiz\QuizGameRepository;
use App\Services\Response\ErrorCode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="quiz_game")
 * @ORM\Entity(repositoryClass=QuizGameRepository::class)
 */
class QuizGame extends Game
{
    public const DEFAULT_SLOTS_IN_TEAM = 3;
    public const MAX_TEAMS = 2;
    public const STRICT_TEAMS = true;
    public const PHASES_COUNT = 3;

    public const GAME_STATUS_CHOOSE_PHASES = 'choose_phases';
    public const GAME_STATUS_PLAYING = 'playing';

    /**
     * @ORM\OneToMany(targetEntity=BasePhase::class, mappedBy="game", orphanRemoval=true)
     * @Groups({"Api", "AMQP"})
     */
    private Collection $phases;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Groups({"Api", "AMQP"})
     */
    private string $gameStatus = self::GAME_STATUS_CHOOSE_PHASES;

    public function __construct()
    {
        parent::__construct();
        $this->phases = new ArrayCollection();
    }

    public function getType(): string
    {
        return self::TYPE_QUIZ;
    }

    public function getPhases(): Collection
    {
        return $this->phases;
    }

    public function addPhase(BasePhase $phase): self
    {
        if ($this->phases->count() >= self::PHASES_COUNT) {
            throw new AppException(ErrorCode::QUIZ_GAME_HAS_MAX_PHASES);
        }

        if (!$this->phases->contains($phase)) {
            $this->phases[] = $phase;
            $phase->setGame($this);
        }

        return $this;
    }

    public function removePhase(BasePhase $phase): self
    {
        if ($this->phases->contains($phase)) {
            $this->phases->removeElement($phase);
            // set the owning side to null (unless already changed)
            if ($phase->getGame() === $this) {
                $phase->setGame(null);
            }
        }

        return $this;
    }

    public function getGameStatus(): string
    {
        return $this->gameStatus;
    }

    public function setGameStatus(string $gameStatus): self
    {
        $this->gameStatus = $gameStatus;

        return $this;
    }

    public function getCurrentPhase(): ?BasePhase
    {
        foreach ($this->phases as $phase) {
            /** @var BasePhase $phase */
            if ($phase->getStatus() === BasePhase::STATUS_IN_PROGRESS) {
                return $phase;
            }
        }

        return null;
    }

    public function getCurrentQuestion(): ?QuestionInterface
    {
        $phase = $this->getCurrentPhase();

        if ($phase) {
            return $phase->getCurrentQuestion();
        }

        return null;
    }

    public function isLastPhase(): bool
    {
        /** @var BasePhase $lastPhase */
        $lastPhase = $this->phases->last();

        return $lastPhase->getStatus() === BasePhase::STATUS_IN_PROGRESS;
    }

    public function finishCurrentPhase(): void
    {
        foreach ($this->phases as $phase) {
            /** @var BasePhase $phase */
            if ($phase->getStatus() === BasePhase::STATUS_IN_PROGRESS) {
                $phase->setStatus(BasePhase::STATUS_FINISHED);
            }

            if ($phase->getStatus() === BasePhase::STATUS_PREPARED) {
                $phase->setStatus(BasePhase::STATUS_IN_PROGRESS);
                return;
            }
        }
    }

    public function getPreparedPhase(): ?BasePhase
    {
        foreach ($this->phases as $phase) {
            if ($phase->getStatus() === BasePhase::STATUS_PREPARED) {
                return $phase;
            }
        }

        return null;
    }

    public function isAllPhasesFinished(): bool
    {
        foreach ($this->phases as $phase) {
            /** @var BasePhase $phase */
            if ($phase->getStatus() !== BasePhase::STATUS_FINISHED) {
                return false;
            }
        }

        return true;
    }
}
