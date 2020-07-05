<?php

namespace App\Repository\Game\Quiz\Phase\Questions;

use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuestionsPhaseQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionsPhaseQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionsPhaseQuestion[]    findAll()
 * @method QuestionsPhaseQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionsPhaseQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionsPhaseQuestion::class);
    }

    // /**
    //  * @return QuestionsPhaseQuestion[] Returns an array of QuestionsPhaseQuestion objects
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
    public function findOneBySomeField($value): ?QuestionsPhaseQuestion
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
