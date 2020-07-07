<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\AnswerInterface;
use App\Repository\Game\Quiz\Phase\Questions\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
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
    private string $answer;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"Exclude"})
     */
    private bool $correct = false;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsQuestion::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private QuestionsQuestion $question;

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
