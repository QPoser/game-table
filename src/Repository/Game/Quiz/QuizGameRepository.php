<?php

namespace App\Repository\Game\Quiz;

use App\Entity\Game\Quiz\QuizGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizGame[]    findAll()
 * @method QuizGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizGame::class);
    }

    // /**
    //  * @return QuizGame[] Returns an array of QuizGame objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuizGame
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
