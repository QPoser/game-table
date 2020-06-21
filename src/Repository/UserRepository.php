<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Game\Game;
use App\Entity\Game\GamePlayer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder
            ->select('u.email')
            ->innerJoin('u.gamePlayers', 'urp')
            ->innerJoin('urp.game', 'ur')
            ->andWhere(
                $queryBuilder->expr()->eq('ur.id', ':gameId'),
                $queryBuilder->expr()->in('urp.status', ':activeStatuses')
            )
            ->setParameters([
                'gameId' => $game->getId(),
                'activeStatuses' => GamePlayer::ACTIVE_STATUSES
            ]);

        return $queryBuilder->getQuery()->getResult();
    }
}
