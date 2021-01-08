<?php

declare(strict_types=1);

namespace App\Repository\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class QuestionsPhaseQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionsPhaseQuestion::class);
    }
}
