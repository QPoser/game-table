<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Repository\Game\Quiz\Phase\Questions\QuestionsPhaseQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionsPhaseQuestionRepository::class)
 */
class QuestionsPhaseQuestion
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_CURRENT = 'current';
    public const STATUS_ANSWERED = 'answered';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Question $question;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsPhase::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private QuestionsPhase $phase;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private string $status = self::STATUS_WAIT;

    /**
     * @ORM\OneToMany(targetEntity=QuestionsPhaseAnswer::class, mappedBy="phaseQuestion", orphanRemoval=true)
     */
    private $phaseAnswers;

    public function __construct()
    {
        $this->phaseAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getPhase(): ?QuestionsPhase
    {
        return $this->phase;
    }

    public function setPhase(?QuestionsPhase $phase): self
    {
        $this->phase = $phase;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|QuestionsPhaseAnswer[]
     */
    public function getPhaseAnswers(): Collection
    {
        return $this->phaseAnswers;
    }

    public function addPhaseAnswer(QuestionsPhaseAnswer $phaseAnswer): self
    {
        if (!$this->phaseAnswers->contains($phaseAnswer)) {
            $this->phaseAnswers[] = $phaseAnswer;
            $phaseAnswer->setPhaseQuestion($this);
        }

        return $this;
    }

    public function removePhaseAnswer(QuestionsPhaseAnswer $phaseAnswer): self
    {
        if ($this->phaseAnswers->contains($phaseAnswer)) {
            $this->phaseAnswers->removeElement($phaseAnswer);
            // set the owning side to null (unless already changed)
            if ($phaseAnswer->getPhaseQuestion() === $this) {
                $phaseAnswer->setPhaseQuestion(null);
            }
        }

        return $this;
    }
}