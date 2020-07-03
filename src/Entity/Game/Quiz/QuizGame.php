<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Exception\AppException;
use App\Repository\Game\Quiz\QuizGameRepository;
use App\Services\Response\ErrorCode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private Collection $phases;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
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
}
