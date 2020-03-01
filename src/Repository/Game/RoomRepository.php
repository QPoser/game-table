<?php
declare(strict_types=1);

namespace App\Repository\Game;

use App\Entity\Game\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findRoomBySlugForUser(string $slug, User $user, bool $strict = true): ?Room
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->innerJoin('r.roomPlayers', 'rp')
            ->innerJoin('rp.player', 'rpp')
            ->andWhere(
                $qb->expr()->eq('rpp.id', ':player'),
                $qb->expr()->in('rp.role', ':roles'),
                $qb->expr()->in('r.slug', ':slug'),
            );
    }
}
