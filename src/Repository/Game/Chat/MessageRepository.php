<?php
declare(strict_types=1);

namespace App\Repository\Game\Chat;

use App\Entity\Game\Chat\Message;
use App\Entity\Game\Game;
use App\Entity\User;
use App\Helper\PaginationHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use function Doctrine\ORM\QueryBuilder;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getMessagesByGameWithPagination(
        Game $game,
        User $user,
        int $limit,
        int $offset,
        ?array $sorting = []
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('m');

        $queryBuilder
            ->leftJoin('m.team', 'mt')
            ->leftJoin('mt.players', 'mtp')
            ->andWhere(
                $queryBuilder->expr()->eq('m.game', ':game'),
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('m.type', ':gameType'),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('m.type', ':teamType'),
                        $queryBuilder->expr()->eq('mtp.team', 'm.team'),
                        $queryBuilder->expr()->eq('mtp.user', ':user'),
                    )
                )
            )
            ->setParameter('gameType', Message::TYPE_GAME)
            ->setParameter('teamType', Message::TYPE_TEAM)
            ->setParameter('user', $user)
            ->setParameter('game', $game);

        $queryBuilder
            ->addOrderBy('m.id', 'DESC')
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
