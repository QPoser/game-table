<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\PhaseQuestionInterface;
use App\Entity\Game\Quiz\Phase\QuestionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Game\Quiz\Phase\Questions\QuestionsPhaseRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="phase_questions")
 * @ORM\Entity(repositoryClass=QuestionsPhaseRepository::class)
 */
class QuestionsPhase extends BasePhase
{
    /**
     * @ORM\OneToMany(targetEntity=QuestionsPhaseQuestion::class, mappedBy="phase", orphanRemoval=true)
     * @Groups({"Exclude"})
     */
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getType(): string
    {
        return self::TYPE_QUESTIONS;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(QuestionsPhaseQuestion $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setPhase($this);
        }

        return $this;
    }

    public function removeQuestion(QuestionsPhaseQuestion $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getPhase() === $this) {
                $question->setPhase(null);
            }
        }

        return $this;
    }

    public function play(): void
    {
        /** @var QuestionsPhaseQuestion $question */
        $question = $this->questions->first();
        $question->setStatus(QuestionsPhaseQuestion::STATUS_CURRENT);

        parent::play();
    }

    public function getCurrentPhaseQuestion(): ?PhaseQuestionInterface
    {
        foreach ($this->questions as $question) {
            /** @var QuestionsPhaseQuestion $question */
            if ($question->getStatus() === QuestionsPhaseQuestion::STATUS_CURRENT) {
                return $question;
            }
        }

        return null;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function getCurrentQuestion(): ?QuestionInterface
    {
        $currentPhaseQuestion = $this->getCurrentPhaseQuestion();

        return $currentPhaseQuestion ? $currentPhaseQuestion->getQuestion() : null;
    }

    public function isFreeAnswer(): bool
    {
        return false;
    }

    public function isLastQuestion(): bool
    {
        /** @var QuestionsPhaseQuestion $lastQuestion */
        $lastQuestion = $this->questions->last();

        return $lastQuestion->getStatus() === QuestionsPhaseQuestion::STATUS_CURRENT;
    }

    public function closeQuestion(): void
    {
        foreach ($this->questions as $question) {
            /** @var QuestionsPhaseQuestion $question */

            if ($question->getStatus() === QuestionsPhaseQuestion::STATUS_CURRENT) {
                $question->setStatus(QuestionsPhaseQuestion::STATUS_ANSWERED);
            }

            if ($question->getStatus() === QuestionsPhaseQuestion::STATUS_WAIT) {
                $question->setStatus(QuestionsPhaseQuestion::STATUS_CURRENT);
            }
        }
    }

    public function isAllQuestionsFinished(): bool
    {
        foreach ($this->questions as $question) {
            /** @var QuestionsPhaseQuestion $question */

            if ($question->getStatus() !== QuestionsPhaseQuestion::STATUS_ANSWERED) {
                return false;
            }
        }

        return true;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function getQuestionsInProgress(): Collection
    {
        return $this->questions->filter(static function ($question) {
            /** @var QuestionsPhaseQuestion $question */
            return $question->getStatus() !== QuestionsPhaseQuestion::STATUS_WAIT;
        });
    }
}