<?php
declare(strict_types=1);

namespace App\Services\Friends;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FriendService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function getUserFriends(UserInterface $user): array
    {
        return  $this->entityManager->getRepository(User::class)->getUserFriends($user);
    }
}