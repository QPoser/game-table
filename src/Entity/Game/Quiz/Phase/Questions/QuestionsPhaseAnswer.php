<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase\Questions;

use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Repository\Game\Quiz\Phase\Questions\QuestionsPhaseAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionsPhaseAnswerRepository::class)
 */
class QuestionsPhaseAnswer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=QuestionsPhaseQuestion::class, inversedBy="phaseAnswers")
     * @ORM\JoinColumn(nullable=false)
     */
    private QuestionsPhaseQuestion $phaseQuestion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=GameTeam::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private GameTeam $team;

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
}
