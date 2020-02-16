<?php
declare(strict_types=1);

namespace App\Services\User;

use App\Entity\User;
use App\Services\Mailer\UserRegisterMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterService
{
    private EntityManagerInterface $em;

    private UserPasswordEncoderInterface $userPasswordEncoder;

    private UserRegisterMailer $userRegisterMailer;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoder, UserRegisterMailer $userRegisterMailer)
    {
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRegisterMailer = $userRegisterMailer;
    }

    public function registerUser(string $email, string $username, string $password): User
    {
        $this->em->beginTransaction();
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
        $user->setVerifyToken(uniqid());

        $this->em->persist($user);
        $this->em->flush($user);

        $this->userRegisterMailer->sendUserRegisterMail($user);
        $this->em->commit();

        return $user;
    }

    public function verifyRegister(string $token): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['verifyToken' => $token]);

        if (!$user) {
            throw new \RuntimeException('User not found by token');
        }

        $user->setVerifyToken(null);
        $this->em->flush($user);
    }
}