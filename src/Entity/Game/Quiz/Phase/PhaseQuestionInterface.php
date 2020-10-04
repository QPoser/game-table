<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

use Doctrine\Common\Collections\Collection;

interface PhaseQuestionInterface
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_CURRENT = 'current';
    public const STATUS_ANSWERED = 'answered';
    public const STATUS_COUNTED = 'counted';

    public function getQuestion(): ?QuestionInterface;

    public function getPhaseAnswers(): Collection;

    public function setStatus(string $status): self;

    public function getStatus(): ?string;
}