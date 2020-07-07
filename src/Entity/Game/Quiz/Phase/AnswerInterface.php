<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz\Phase;

interface AnswerInterface
{
    public function getId(): ?int;

    public function getQuestion(): ?QuestionInterface;

    public function getAnswer(): ?string;

    public function isCorrect(): bool;
}