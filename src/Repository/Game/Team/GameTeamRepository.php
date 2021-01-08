<?php

declare(strict_types=1);

namespace App\Repository\Game\Team;

use App\Entity\Game\Team\GameTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTeam[]    findAll()
 * @method GameTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GameTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTeam::class);
    }

    // /**
    //  * @return GameTeam[] Returns an array of GameTeam objects
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
    public function findOneBySomeField($value): ?GameTeam
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
