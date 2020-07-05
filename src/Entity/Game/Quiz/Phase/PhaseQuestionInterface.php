<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

interface PhaseQuestionInterface
{
    public function getQuestion(): ?QuestionInterface;
}