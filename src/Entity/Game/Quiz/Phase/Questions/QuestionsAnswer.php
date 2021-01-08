<?php

declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\AnswerInterface;
use App\Repository\Game\Quiz\Phase\Questions\QuestionsAnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=QuestionsAnswerRepository::class)
 * @UniqueEntity(fields={"answer", "question"}, message="Answer is unique for one question")
 */
class QuestionsAnswer implements AnswerInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Api", "AMQP"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Api", "AMQP"})
     */
    private ?string $answer = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Exclude"})
     */
    private bool $correct = false;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsQuestion::class, inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private ?QuestionsQuestion $question = null;

    public function __toString()
    {
        return $this->answer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function setCorrect(bool $correct): self
    {
        $this->correct = $correct;

        return $this;
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

    public function isCorrect(): bool
    {
        return $this->correct;
    }
}
