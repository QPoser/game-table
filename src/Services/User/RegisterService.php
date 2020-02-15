<?php
declare(strict_types=1);

namespace App\Services\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterService
{
    private EntityManagerInterface $em;

    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function registerUser(string $email, string $username, string $password): User
    {
        $userRepository = $this->em->getRepository(User::class);

        if ($userRepository->findOneBy(['email' => $email]) || $userRepository->findOneBy(['username' => $username])) {
            throw new \RuntimeException('User already exists in database');
        }

        $user = new User();

        $password = $this->userPasswordEncoder->encodePassword(
            $user,
            $password
        );

        $user->setEmail($email);
        $user->setPassword($password);
        $user->setUsername($username);
        $user->setRoles([User::ROLE_USER]);

        $this->em->persist($user);
        $this->em->flush($user);

        return $user;
    }
}