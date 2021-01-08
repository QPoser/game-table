<?php

declare(strict_types=1);

namespace App\Repository\Game\Quiz\Phase;

use App\Entity\Game\Quiz\Phase\BasePhase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class BasePhaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BasePhase::class);
    }
}
