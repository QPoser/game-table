<?php

declare(strict_types=1);

namespace App\Repository\Game\Quiz;

use App\Entity\Game\Quiz\QuizGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class QuizGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizGame::class);
    }
}
