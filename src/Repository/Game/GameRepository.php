<?php

declare(strict_types=1);

namespace App\Repository\Game;

use App\Entity\Game\Game;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Helper\PaginationHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getGamesWithPagination(int $limit, int $offset, string $access = Game::ACCESS_PUBLIC, ?array $sorting = []): array
    {
        $queryBuilder = $this->createQueryBuilder('g');

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq('g.access', ':access'),
                $queryBuilder->expr()->eq('g.status', ':startedStatus')
            )
            ->setParameter('startedStatus', Game::STATUS_CREATED)
            ->setParameter('access', $access);

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

    public function getCurrentUserGame(User $user): ?Game
    {
        $queryBuilder = $this->createQueryBuilder('g');

        $queryBuilder
            ->innerJoin(GameTeam::class, 'gt', Join::WITH, 'gt.game = g.id')
            ->innerJoin('gt.players', 'gtp')
            ->andWhere(
                $queryBuilder->expr()->eq('gtp.user', ':user'),
                $queryBuilder->expr()->in('g.status', ':progressedStatuses')
            )
            ->setParameter('user', $user)
            ->setParameter('progressedStatuses', [Game::STATUS_STARTED, Game::STATUS_CREATED])
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
