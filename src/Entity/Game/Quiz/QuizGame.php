<?php
declare(strict_types=1);

namespace App\Entity\Game\Quiz;

use App\Entity\Game\Game;
use App\Repository\Game\Quiz\QuizGameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="quiz_game")
 * @ORM\Entity(repositoryClass=QuizGameRepository::class)
 */
class QuizGame extends Game
{
    public const DEFAULT_SLOTS_IN_TEAM = 3;
    public const MAX_TEAMS = 2;
    public const STRICT_TEAMS = true;

    public function getType(): string
    {
        return self::TYPE_QUIZ;
    }
}
