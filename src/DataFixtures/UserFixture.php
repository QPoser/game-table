<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $password = 'secret';
        $roles = [
            User::ROLE_ADMIN,
            User::ROLE_ADMIN,
        ];

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail('user-' . $i . '@app.com');
            $user->setUsername('User' . $i);
            $user->setRoles([$roles[$i - 1] ?? User::ROLE_USER]);
            $user->setPassword($this->userPasswordEncoder->encodePassword(
                $user,
                $password
            ));

            $manager->persist($user);
            $manager->flush();
        }
    }
}
