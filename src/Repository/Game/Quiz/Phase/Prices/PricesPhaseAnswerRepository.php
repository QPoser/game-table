<?php
declare(strict_types=1);

namespace App\Repository\Game\Quiz\Phase\Prices;

use App\Entity\Game\Quiz\Phase\Prices\PricesPhaseAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PricesPhaseAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PricesPhaseAnswer::class);
    }
}
