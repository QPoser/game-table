<?php

declare(strict_types=1);

namespace App\Repository\Game\Quiz\Phase\Prices;

use App\Entity\Game\Quiz\Phase\Prices\PricesPhaseQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PricesPhaseQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PricesPhaseQuestion::class);
    }
}
