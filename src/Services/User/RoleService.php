<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class RoleService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function applyAdminRole(User $user): void
    {
        $user->addRole(User::ROLE_ADMIN);
        $this->em->flush();
    }
}
