<?php

namespace App\DataFixtures;

use App\Entity\FriendRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FriendRequestFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [FriendRequest::STATUS_ACCEPTED, FriendRequest::STATUS_DECLINED, FriendRequest::STATUS_SENT];
        $from = [1, 2];
        $to = [2, 3];

        for ($i = 1; $i <= 2; $i++) {
            $userFrom = $this->getReference('user_' . $from[$i - 1]);
            $userTo = $this->getReference('user_' . $to[$i - 1]);

            $fr = new FriendRequest();
            $fr->setStatus($statuses[$i-1]);
            $fr->setUserFrom($userFrom);
            $fr->setUserTo($userTo);
            $manager->persist($fr);

        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixture::class];
    }
}
