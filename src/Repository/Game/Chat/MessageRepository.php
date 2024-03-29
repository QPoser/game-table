<?php

declare(strict_types=1);

namespace App\Repository\Game\Chat;

use App\Dto\RequestDto\PaginationRequest;
use App\Entity\Game\Chat\Message;
use App\Entity\Game\Game;
use App\Entity\User;
use App\Helper\PaginationHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class MessageRepository extends ServiceEntityRepository
{
    private const DEFAULT_MESSAGES_LIMIT = 60;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getMessagesByGameWithPagination(
        Game $game,
        User $user,
        PaginationRequest $paginationDto,
        ?array $sorting = []
    ): array {
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
            ->setParameter('game', $game)
            ->addOrderBy('m.id', 'DESC');

        $limit = $paginationDto->getLimit() ?? self::DEFAULT_MESSAGES_LIMIT;
        $offset = $paginationDto->getOffset();

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
