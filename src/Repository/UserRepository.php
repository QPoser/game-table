<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Game\Game;
use App\Entity\Game\Team\GameTeam;
use App\Entity\Game\Team\GameTeamPlayer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use function Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findUserEmailsByGame(Game $game): array
    {
        $queryBuilder = $this->getSearchQueryBuilderByGame($game);
        $queryBuilder->select('u.email');

        return array_column($queryBuilder->getQuery()->getResult(), 'email');
    }

    public function findUserEmailsByTeam(GameTeam $team): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder
            ->select('u.email')
            ->innerJoin(GameTeamPlayer::class, 'gtp', 'WITH', 'gtp.user = u.id')
            ->innerJoin('gtp.team', 'gtpt')
            ->andWhere(
                $queryBuilder->expr()->eq('gtpt.id', ':teamId')
            )
            ->setParameters([
                'teamId' => $team->getId()
            ]);

        return array_column($queryBuilder->getQuery()->getResult(), 'email');
    }

    public function findUsersByGame(Game $game): array
    {
        return $this->getSearchQueryBuilderByGame($game)->getQuery()->getResult();
    }

    private function getSearchQueryBuilderByGame(Game $game): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder
            ->innerJoin(GameTeamPlayer::class, 'gtp', 'WITH', 'gtp.user = u.id')
            ->innerJoin('gtp.team', 'gtpt')
            ->innerJoin('gtpt.game', 'game')
            ->andWhere(
                $queryBuilder->expr()->eq('game.id', ':gameId')
            )
            ->setParameters([
                'gameId' => $game->getId()
            ]);

        return $queryBuilder;
    }
}
