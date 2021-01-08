<?php

declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\PhaseQuestionInterface;
use App\Repository\Game\Quiz\Phase\Questions\QuestionsPhaseQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=QuestionsPhaseQuestionRepository::class)
 */
class QuestionsPhaseQuestion implements PhaseQuestionInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsQuestion::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private QuestionsQuestion $question;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsPhase::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private QuestionsPhase $phase;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups({"Api", "AMQP"})
     */
    private string $status = self::STATUS_WAIT;

    /**
     * @ORM\OneToMany(targetEntity=QuestionsPhaseAnswer::class, mappedBy="phaseQuestion", orphanRemoval=true)
     * @Groups({"Exclude"})
     */
    private Collection $phaseAnswers;

    public function __construct()
    {
        $this->phaseAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?QuestionsQuestion
    {
        return $this->question;
    }

    public function setQuestion(?QuestionsQuestion $question): self
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

    public function getFormattedPhaseAnswers(): ?Collection
    {
        if ($this->status === self::STATUS_ANSWERED) {
            return $this->phaseAnswers;
        }

        return null;
    }
}
