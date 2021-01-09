<?php

declare(strict_types=1);

namespace App\Repository\Game\Team;

use App\Entity\Game\Team\GameTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class GameTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTeam::class);
    }
}
