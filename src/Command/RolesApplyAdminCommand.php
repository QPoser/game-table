<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Services\User\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RolesApplyAdminCommand extends Command
{
    protected static $defaultName = 'app:roles:apply-admin';

    private RoleService $roleService;

    private EntityManagerInterface $em;

    public function __construct(string $name = null, RoleService $roleService, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->roleService = $roleService;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Apply admin role for user by email')
            ->addArgument('email', InputArgument::REQUIRED, 'User email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error('User ' . $email . ' not found!');

            return 0;
        }

        $this->roleService->applyAdminRole($user);
        $io->success('Admin role applied for user ' . $email);

        return 0;
    }
}
