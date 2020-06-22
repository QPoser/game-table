<?php
declare(strict_types=1);

namespace App\Repository\Game;

use App\Entity\Game\Game;
use App\Entity\User;
use App\Helper\PaginationHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use function Doctrine\ORM\QueryBuilder;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getGamesWithPagination(int $limit, int $offset, string $access = Game::ACCESS_PUBLIC, ?array $sorting = []): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq('r.access', ':access')
            )
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
}
