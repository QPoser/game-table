<?php

namespace App\DataFixtures;

use App\Entity\Game\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $passwords = [
            'secret',
            'secret',
        ];

        for ($i = 1; $i <= 5; $i++) {
            $room = new Room();
            $room->setTitle('Room ' . $i);
            $room->setSlots(10);
            $room->setRules('Default rules');
            $room->setPassword($passwords[$i - 1] ?? null);

            $manager->persist($room);
            $manager->flush();
        }
    }
}
