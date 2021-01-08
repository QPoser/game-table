<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\QuizGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class GameFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $passwords = [
            'secret',
            'secret',
        ];

        for ($i = 1; $i <= 5; $i++) {
            $game = new QuizGame();
            $game->setTitle('Game ' . $i);
            $game->setPassword($passwords[$i - 1] ?? null);
            $game->setAutoCreated(false);
            $game->setStatus(Game::STATUS_CREATED);

            $manager->persist($game);
            $manager->flush();

            $this->addReference('game_' . $i, $game);
        }
    }
}
