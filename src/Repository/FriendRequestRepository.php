<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\FriendRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method FriendRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendRequest[]    findAll()
 * @method FriendRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public const DIRECTION_FROM = 'from';
    public const DIRECTION_TO = 'to';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }

    public function getUserRequests(UserInterface $user, array $directions): array
    {
        $result = [];

        if (array_key_exists('from', $directions)) {
            $result[self::DIRECTION_FROM] = $this->getUserFromRequests($user);
        }

        if (array_key_exists('to', $directions)) {
            $result[self::DIRECTION_TO] = $this->getUserToRequests($user);
        }

        return $result;
    }

    public function getUserFromRequests(UserInterface $user): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.userFrom = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getUserToRequests(UserInterface $user): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.userTo = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

}

