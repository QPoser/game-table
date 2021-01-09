<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Dto\RegisterDTOInterface;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Mailer\UserRegisterMailer;
use App\Services\Response\ErrorCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class RegisterService
{
    private EntityManagerInterface $em;

    private UserPasswordEncoderInterface $userPasswordEncoder;

    private UserRegisterMailer $userRegisterMailer;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRegisterMailer $userRegisterMailer
    ) {
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRegisterMailer = $userRegisterMailer;
    }

    public function registerUser(RegisterDTOInterface $registerUserDto): User
    {
        $email = $registerUserDto->getEmail();
        $username = $registerUserDto->getUsername();
        $password = $registerUserDto->getPassword();

        $this->em->beginTransaction();
        $userRepository = $this->em->getRepository(User::class);

        if ($userRepository->findOneBy(['email' => $email]) || $userRepository->findOneBy(['username' => $username])) {
            throw new AppException(ErrorCode::USER_ALREADY_EXISTS_IN_DATABASE);
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
        $user->setVerifyToken(uniqid('', true));

        $this->em->persist($user);
        $this->em->flush();

        $this->userRegisterMailer->sendUserRegisterMail($user);
        $this->em->commit();

        return $user;
    }

    public function verifyRegister(string $token): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['verifyToken' => $token]);

        if (!$user) {
            throw new AppException(ErrorCode::USER_NOT_FOUND_BY_TOKEN);
        }

        $user->setVerifyToken(null);
        $this->em->flush();
    }
}
