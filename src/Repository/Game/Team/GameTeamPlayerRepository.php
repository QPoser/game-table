<?php

declare(strict_types=1);

namespace App\Repository\Game\Team;

use App\Entity\Game\Team\GameTeamPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameTeamPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTeamPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTeamPlayer[]    findAll()
 * @method GameTeamPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GameTeamPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTeamPlayer::class);
    }

    // /**
    //  * @return GameTeamPlayer[] Returns an array of GameTeamPlayer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GameTeamPlayer
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
