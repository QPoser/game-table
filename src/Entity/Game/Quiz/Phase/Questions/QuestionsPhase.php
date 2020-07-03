<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\BasePhase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Game\Quiz\Phase\Questions\QuestionsPhaseRepository;

/**
 * @ORM\Table(name="phase_questions")
 * @ORM\Entity(repositoryClass=QuestionsPhaseRepository::class)
 */
class QuestionsPhase extends BasePhase
{
    /**
     * @ORM\OneToMany(targetEntity=QuestionsPhaseQuestion::class, mappedBy="phase", orphanRemoval=true)
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
}