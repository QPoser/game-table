<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Repository\Game\Quiz\Phase\Questions\QuestionsPhaseAnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=QuestionsPhaseAnswerRepository::class)
 */
class QuestionsPhaseAnswer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"Exclude"})
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsPhaseQuestion::class, inversedBy="phaseAnswers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Exclude"})
     */
    private QuestionsPhaseQuestion $phaseQuestion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=GameTeam::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Api", "AMQP"})
     */
    private GameTeam $team;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Exclude"})
     */
    private string $answer;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsAnswer::class)
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Exclude"})
     */
    private QuestionsAnswer $questionsAnswer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhaseQuestion(): ?QuestionsPhaseQuestion
    {
        return $this->phaseQuestion;
    }

    public function setPhaseQuestion(?QuestionsPhaseQuestion $phaseQuestion): self
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

    public function getQuestionsAnswer(): ?QuestionsAnswer
    {
        return $this->questionsAnswer;
    }

    public function setQuestionsAnswer(?QuestionsAnswer $questionsAnswer): self
    {
        $this->questionsAnswer = $questionsAnswer;

        return $this;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function getFormattedAnswer(): ?string
    {
        if ($this->phaseQuestion->getStatus() === QuestionsPhaseQuestion::STATUS_ANSWERED) {
            return $this->answer;
        }

        return null;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function getFormattedQuestionsAnswer(): ?QuestionsAnswer
    {
        if ($this->phaseQuestion->getStatus() === QuestionsPhaseQuestion::STATUS_ANSWERED) {
            return $this->questionsAnswer;
        }

        return null;
    }

    /**
     * @Groups({"Api", "AMQP"})
     */
    public function isCorrect(): ?bool
    {
        if (!$this->questionsAnswer) {
            return null;
        }

        if ($this->phaseQuestion->getStatus() === QuestionsPhaseQuestion::STATUS_ANSWERED) {
            return $this->phaseQuestion->getQuestion()->isCorrectAnswer($this->questionsAnswer);
        }

        return null;
    }
}
