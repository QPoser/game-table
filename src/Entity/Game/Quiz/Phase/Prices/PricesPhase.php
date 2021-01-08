<?php

declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Prices;

use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\QuestionInterface;
use App\Repository\Game\Quiz\Phase\Prices\PricesPhaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="phase_prices")
 * @ORM\Entity(repositoryClass=PricesPhaseRepository::class)
 */
class PricesPhase extends BasePhase
{
    /**
     * @ORM\OneToMany(targetEntity=PricesPhaseQuestion::class, mappedBy="phase", orphanRemoval=true)
     * @Groups({"Exclude"})
     */
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getType(): string
    {
        return self::TYPE_PRICES;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(PricesPhaseQuestion $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setPhase($this);
        }

        return $this;
    }

    public function removeQuestion(PricesPhaseQuestion $question): self
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
        /** @var PricesPhaseQuestion $question */
        $question = $this->questions->first();
        $question->setStatus(PricesPhaseQuestion::STATUS_CURRENT);

        parent::play();
    }

    public function getCurrentPhaseQuestion(): ?PricesPhaseQuestion
    {
        foreach ($this->questions as $question) {
            /** @var PricesPhaseQuestion $question */
            if ($question->getStatus() === PricesPhaseQuestion::STATUS_CURRENT) {
                return $question;
            }
        }

        return null;
    }

    public function getAnsweredPhaseQuestions(): ArrayCollection
    {
        return $this->questions->filter(static fn ($question) => $question->getStatus() === PricesPhaseQuestion::STATUS_ANSWERED);
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
        return true;
    }

    public function isLastQuestion(): bool
    {
        /** @var PricesPhaseQuestion $lastQuestion */
        $lastQuestion = $this->questions->last();

        return $lastQuestion->getStatus() === PricesPhaseQuestion::STATUS_CURRENT;
    }

    public function closeQuestion(): void
    {
        foreach ($this->questions as $question) {
            /** @var PricesPhaseQuestion $question */
            if ($question->getStatus() === PricesPhaseQuestion::STATUS_CURRENT) {
                $question->setStatus(PricesPhaseQuestion::STATUS_ANSWERED);
            }

            if ($question->getStatus() === PricesPhaseQuestion::STATUS_WAIT) {
                $question->setStatus(PricesPhaseQuestion::STATUS_CURRENT);

                return;
            }
        }
    }

    public function isAllQuestionsFinished(): bool
    {
        foreach ($this->questions as $question) {
            /** @var PricesPhaseQuestion $question */
            if ($question->getStatus() !== PricesPhaseQuestion::STATUS_ANSWERED) {
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
            /* @var PricesPhaseQuestion $question */
            return $question->getStatus() !== PricesPhaseQuestion::STATUS_WAIT;
        });
    }
}
