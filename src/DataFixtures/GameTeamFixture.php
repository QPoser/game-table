<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GameTeamFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 2; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                $team = new GameTeam();
                $team->setTitle('Birds');
                $team->setSlots(2);
                $team->setGame($this->getReference('game_' . $j));

                $manager->persist($team);
                $manager->flush();
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            GameFixture::class,
        ];
    }
}
