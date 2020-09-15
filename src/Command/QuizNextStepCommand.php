<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Game\Quiz\QuizGame;
use App\Services\Game\Quiz\QuizGameService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QuizNextStepCommand extends Command
{
    protected static $defaultName = 'app:quiz:next-step';

    /**
     * @var QuizGameService
     */
    private QuizGameService $quizGameService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(string $name = null, QuizGameService $quizGameService, EntityManagerInterface $em)
    {
        $this->quizGameService = $quizGameService;
        $this->em = $em;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Command for launching next step of quiz game')
            ->addOption('game_id', null, InputOption::VALUE_REQUIRED, 'Game id')
            ->addOption('timestamp', null, InputOption::VALUE_REQUIRED, 'Timestamp')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $gameId = $input->getOption('game_id');
        $timestamp = $input->getOption('timestamp');

        if (!$gameId || !$timestamp) {
            $io->error('You should pass arguments: game_id, timestamp');
            $io->error('Current timestamp: ' . time());
            return 1;
        }

        $game = $this->em->getRepository(QuizGame::class)->find($gameId);

        if (!($game instanceof QuizGame)) {
            $io->error('Game not found');
            return 1;
        }

        if ($game->getCurrentStepSeconds() !== null) {
            $io->error('Current game step have time');
            return 1;
        }

        if (!$game->getLastAction() || (int)$timestamp < $game->getLastAction()->getTimestamp()) {
            $io->error('Game next step command is not in time');
            return 1;
        }

        $this->quizGameService->nextStep($game);
        $io->success('OK!');

        return 0;
    }
}
