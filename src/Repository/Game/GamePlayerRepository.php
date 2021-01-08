<?php

declare(strict_types=1);

namespace App\Repository\Game;

use App\Entity\Game\GamePlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GamePlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method GamePlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method GamePlayer[]    findAll()
 * @method GamePlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GamePlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GamePlayer::class);
    }

    // /**
    //  * @return GamePlayer[] Returns an array of GamePlayer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GamePlayer
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
