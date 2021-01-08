<?php

declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

interface QuestionInterface
{
    public function getQuestion(): ?string;

    public function isCorrectAnswer(AnswerInterface $answer): bool;

    public function getAnswerByString(string $answer): ?AnswerInterface;
}
