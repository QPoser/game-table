<?php

declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Prices;

use App\Entity\Game\Quiz\Phase\AnswerInterface;
use App\Entity\Game\Quiz\Phase\QuestionInterface;
use App\Repository\Game\Quiz\Phase\Prices\PricesQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PricesQuestionRepository::class)
 */
class PricesQuestion implements QuestionInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"Api", "AMQP"})
     */
    private ?string $question = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Exclude"})
     */
    private bool $enabled = false;

    /**
     * @ORM\OneToMany(targetEntity=PricesAnswer::class, mappedBy="question", orphanRemoval=true, cascade={"persist"})
     * @Groups({"Exclude"})
     */
    private Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isCorrectAnswer(AnswerInterface $answer): bool
    {
        return true;
    }

    public function getAnswerByString(string $userAnswer): ?AnswerInterface
    {
        return null;
    }

    public function setAnswers(array $answers): self
    {
        $answers = new ArrayCollection($answers);

        foreach ($answers as $answer) {
            $answer->setQuestion($this);
        }

        $this->answers = $answers;

        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(PricesAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(PricesAnswer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }
}
