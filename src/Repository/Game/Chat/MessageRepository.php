<?php
declare(strict_types=1);

namespace App\Repository\Game\Chat;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Room;
use App\Helper\PaginationHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getMessagesByRoomWithPagination(Room $room, int $limit, int $offset, ?array $sorting = []): array
    {
        $queryBuilder = $this->createQueryBuilder('m');

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq('m.room', ':room')
            )
            ->setParameter('room', $room);

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder);
        $total = count($paginator);

        return [
            $queryBuilder->getQuery()->getResult(),
            PaginationHelper::createPaginationArray($total, $limit, $offset),
        ];
    }
}
