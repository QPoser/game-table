<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Prices;

use App\Entity\Game\Quiz\Phase\AnswerInterface;
use App\Repository\Game\Quiz\Phase\Prices\PricesAnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PricesAnswerRepository::class)
 */
class PricesAnswer implements AnswerInterface
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
    private ?int $answer = null;

    /**
     * @ORM\ManyToOne(targetEntity=PricesQuestion::class, inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, unique=true)
     * @Groups({"Exclude"})
     */
    private ?PricesQuestion $question = null;

    public function __toString()
    {
        return (string)$this->answer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return (string)$this->answer;
    }

    public function getIntAnswer(): ?int
    {
        return $this->answer;
    }

    public function setAnswer(int $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getQuestion(): ?PricesQuestion
    {
        return $this->question;
    }

    public function setQuestion(?PricesQuestion $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function isCorrect(): bool
    {
        return true;
    }
}
