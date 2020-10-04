<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Prices;

use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Repository\Game\Quiz\Phase\Prices\PricesPhaseAnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PricesPhaseAnswerRepository::class)
 */
class PricesPhaseAnswer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=PricesPhaseQuestion::class, inversedBy="phaseAnswers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private ?PricesPhaseQuestion $phaseQuestion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=GameTeam::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private ?GameTeam $team;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Exclude"})
     */
    private ?string $answer;

    /**
     * @ORM\ManyToOne(targetEntity=PricesAnswer::class)
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Exclude"})
     */
    private ?PricesAnswer $pricesAnswer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhaseQuestion(): ?PricesPhaseQuestion
    {
        return $this->phaseQuestion;
    }

    public function setPhaseQuestion(?PricesPhaseQuestion $phaseQuestion): self
    {
        $this->phaseQuestion = $phaseQuestion;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTeam(): ?GameTeam
    {
        return $this->team;
    }

    public function setTeam(?GameTeam $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getPricesAnswer(): ?PricesAnswer
    {
        return $this->pricesAnswer;
    }

    public function setPricesAnswer(?PricesAnswer $pricesAnswer): self
    {
        $this->pricesAnswer = $pricesAnswer;

        return $this;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function getFormattedAnswer(): ?string
    {
        if ($this->phaseQuestion->getStatus() === PricesPhaseQuestion::STATUS_ANSWERED) {
            return $this->answer;
        }

        return null;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function getFormattedPricesAnswer(): ?PricesAnswer
    {
        if ($this->phaseQuestion->getStatus() === PricesPhaseQuestion::STATUS_ANSWERED) {
            return $this->pricesAnswer;
        }

        return null;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function isCorrect(): ?bool
    {
        if (!$this->pricesAnswer) {
            return null;
        }

        if (in_array($this->phaseQuestion->getStatus(), [PricesPhaseQuestion::STATUS_ANSWERED, PricesPhaseQuestion::STATUS_COUNTED], true)) {
            return $this->phaseQuestion->getQuestion()->isCorrectAnswer($this->pricesAnswer);
        }

        return null;
    }
}
