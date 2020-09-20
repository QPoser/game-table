<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

use Doctrine\Common\Collections\Collection;

interface PhaseQuestionInterface
{
    public function getQuestion(): ?QuestionInterface;

    public function getPhaseAnswers(): Collection;
}