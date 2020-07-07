<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\AnswerInterface;
use App\Entity\Game\Quiz\Phase\QuestionInterface;
use App\Repository\Game\Quiz\Phase\Questions\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class QuestionsQuestion implements QuestionInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"Api", "AMQP"})
     */
    private string $question;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Exclude"})
     */
    private bool $enabled = false;

    /**
     * @ORM\OneToMany(targetEntity=QuestionsAnswer::class, mappedBy="question", orphanRemoval=true)
     * @Groups({"Api", "AMQP"})
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

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(QuestionsAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(QuestionsAnswer $answer): self
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

    public function isCorrectAnswer(AnswerInterface $answer): bool
    {
        return $answer->isCorrect();
    }

    public function getAnswerByString(string $userAnswer): ?AnswerInterface
    {
        foreach ($this->answers as $answer) {
            /** @var QuestionsAnswer $answer */
            if ($answer->getAnswer() === $userAnswer) {
                return $answer;
            }
        }

        return null;
    }
}