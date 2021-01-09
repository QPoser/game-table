<?php

declare(strict_types=1);

namespace App\Repository\Game\Team;

use App\Entity\Game\Team\GameTeamPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class GameTeamPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTeamPlayer::class);
    }
}
