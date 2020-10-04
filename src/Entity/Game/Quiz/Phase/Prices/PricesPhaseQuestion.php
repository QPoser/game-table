<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Prices;

use App\Entity\Game\Quiz\Phase\PhaseQuestionInterface;
use App\Repository\Game\Quiz\Phase\Prices\PricesPhaseQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PricesPhaseQuestionRepository::class)
 */
class PricesPhaseQuestion implements PhaseQuestionInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=PricesQuestion::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private ?PricesQuestion $question;

    /**
     * @ORM\ManyToOne(targetEntity=PricesPhase::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private ?PricesPhase $phase;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups({"Api", "AMQP"})
     */
    private string $status = self::STATUS_WAIT;

    /**
     * @ORM\OneToMany(targetEntity=PricesPhaseAnswer::class, mappedBy="phaseQuestion", orphanRemoval=true)
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

    public function getQuestion(): ?PricesQuestion
    {
        return $this->question;
    }

    public function setQuestion(?PricesQuestion $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getPhase(): ?PricesPhase
    {
        return $this->phase;
    }

    public function setPhase(?PricesPhase $phase): self
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

    public function addPhaseAnswer(PricesPhaseAnswer $phaseAnswer): self
    {
        if (!$this->phaseAnswers->contains($phaseAnswer)) {
            $this->phaseAnswers[] = $phaseAnswer;
            $phaseAnswer->setPhaseQuestion($this);
        }

        return $this;
    }

    public function removePhaseAnswer(PricesPhaseAnswer $phaseAnswer): self
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
