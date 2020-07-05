<?php

namespace App\Repository\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\Questions\QuestionsAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuestionsAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionsAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionsAnswer[]    findAll()
 * @method QuestionsAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionsAnswer::class);
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
