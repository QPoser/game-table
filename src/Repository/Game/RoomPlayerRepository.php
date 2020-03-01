<?php

namespace App\Repository\Game;

use App\Entity\Game\RoomPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RoomPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomPlayer[]    findAll()
 * @method RoomPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomPlayer::class);
    }

    // /**
    //  * @return RoomPlayer[] Returns an array of RoomPlayer objects
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
    public function findOneBySomeField($value): ?RoomPlayer
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
