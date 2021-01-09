<?php

declare(strict_types=1);

namespace App\Repository\Command;

use App\Entity\Command\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }
}
