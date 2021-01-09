<?php

declare(strict_types=1);

namespace App\Services\Game\Quiz;

use App\Command\QuizNextStepCommand;
use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\PhaseQuestionInterface;
use App\Entity\Game\Quiz\Phase\Prices\PricesAnswer;
use App\Entity\Game\Quiz\Phase\Prices\PricesPhase;
use App\Entity\Game\Quiz\Phase\Prices\PricesPhaseAnswer;
use App\Entity\Game\Quiz\Phase\Prices\PricesPhaseQuestion;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsAnswer;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhase;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseAnswer;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseQuestion;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Events\QuizGamePhaseFinishedEvent;
use App\Events\QuizGameUserEnteredAnswerEvent;
use App\Exception\AppException;
use App\Services\Command\ConsoleCommandService;
use App\Services\Game\GameActionService;
use App\Services\Game\GamePlayerTurnService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class QuizGameService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private GameNotificationTemplateHelper $gameNTH;

    private GameActionService $gameActionService;

    private QuizPhaseService $quizPhaseService;

    private EventDispatcherInterface $dispatcher;

    private GamePlayerTurnService $gamePlayerTurnService;

    private ConsoleCommandService $consoleCommandService;

    public function __construct(
        EntityManagerInterface $em,
        ValidationService $validator,
        GameNotificationTemplateHelper $gameNTH,
        GameActionService $gameActionService,
        QuizPhaseService $quizPhaseService,
        EventDispatcherInterface $dispatcher,
        GamePlayerTurnService $gamePlayerTurnService,
        ConsoleCommandService $consoleCommandService
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->gameNTH = $gameNTH;
        $this->gameActionService = $gameActionService;
        $this->quizPhaseService = $quizPhaseService;
        $this->dispatcher = $dispatcher;
        $this->gamePlayerTurnService = $gamePlayerTurnService;
        $this->consoleCommandService = $consoleCommandService;
    }

    public function createGame(string $title, ?User $creator = null, ?string $password = null): QuizGame
    {
        $this->em->beginTransaction();
        $game = new QuizGame();

        $game->setTitle($title);
        $game->setPassword($password);
        $game->setAccess(Game::ACCESS_PUBLIC);
        $game->setCreator($creator);
        $game->setAutoCreated($creator ? false : true);

        $this->validator->validateEntity($game);
        $this->prepareTeams($game);

        $this->em->persist($game);
        $this->em->flush();

        $this->quizPhaseService->createPhase(BasePhase::TYPE_QUESTIONS, $game);

        try {
            $this->em->commit();
        } catch (\Throwable $exception) {
            throw $exception;
        }

        return $game;
    }

    private function prepareTeams(QuizGame $game): void
    {
        for ($i = 1; $i <= QuizGame::MAX_TEAMS; $i++) {
            $team = new GameTeam();

            $game->addTeam($team);
            $team->setSlots(QuizGame::DEFAULT_SLOTS_IN_TEAM);

            $this->em->persist($team);

            $team->setTitle('Team ' . $team->getId());
        }
    }

    public function startGame(QuizGame $game, ?User $user = null): void
    {
        $game->setStatus(Game::STATUS_STARTED);
        $this->refreshGameLastAction($game);

        $this->em->persist($game);
        $this->em->flush();

        $this->quizPhaseService->createPhase(BasePhase::TYPE_QUESTIONS, $game);
        $this->gamePlayerTurnService->setFirstTeamPlayerTurn($game);

        $this->gameActionService->createGameStartedActions($game);
        $this->gameNTH->createGameStartedNotifications($game);
    }

    public function addPhase(QuizGame $game, string $phaseType, User $user): void
    {
        $this->quizPhaseService->createPhase($phaseType, $game, $user);
        $this->refreshGameLastAction($game);

        $this->gameActionService->createQuizGamePhaseChosenActions($game, $user, $phaseType);

        if ($game->getPhases()->count() === QuizGame::PHASES_COUNT) {
            $this->startGamePlaying($game);
        } else {
            $this->gamePlayerTurnService->setSecondTeamPlayerTurn($game);
        }
    }

    public function startGamePlaying(QuizGame $game): void
    {
        $game->setGameStatus(QuizGame::GAME_STATUS_PLAYING);
        $this->refreshGameLastAction($game);

        /** @var BasePhase $phase */
        $phase = $game->getPreparedPhase();
        $phase->play();

        $this->em->flush();

        $this->gamePlayerTurnService->setPlayersTurnInEveryTeam($game, 0);
        $this->gameActionService->createQuizGamePlayingActions($game);
    }

    public function putAnswer(QuizGame $game, string $userAnswer, User $user): void
    {
        $currentPhase = $game->getCurrentPhase();

        if (!$currentPhase) {
            throw new AppException(ErrorCode::QUIZ_GAME_HAS_NO_CURRENT_PHASE);
        }

        $currentQuestion = $currentPhase->getCurrentQuestion();

        if (!$currentQuestion) {
            throw new AppException(ErrorCode::QUIZ_GAME_PHASE_HAS_NO_CURRENT_QUESTION);
        }

        $answer = $currentQuestion->getAnswerByString($userAnswer);

        if (!$answer && !$currentPhase->isFreeAnswer()) {
            throw new AppException(ErrorCode::QUIZ_GAME_QUESTION_HAS_NO_THIS_VARIANT);
        }

        $phaseQuestion = $currentPhase->getCurrentPhaseQuestion();

        if (!$phaseQuestion) {
            return; // @TODO check
        }

        switch ($currentPhase->getType()) {
            case BasePhase::TYPE_QUESTIONS:
                    if (!($answer instanceof QuestionsAnswer)) {
                        $answer = null;
                    }

                    $this->createQuestionsAnswer($game, $userAnswer, $user, $phaseQuestion, $answer);
                break;
            case BasePhase::TYPE_PRICES:
                $this->createPricesAnswer($game, $userAnswer, $user, $phaseQuestion);
                break;
        }
    }

    private function createQuestionsAnswer(QuizGame $game, string $userAnswer, User $user, PhaseQuestionInterface $phaseQuestion, ?QuestionsAnswer $questionAnswer): void
    {
        $teamPlayer = $game->getTeamPlayerByUser($user);

        if (!$teamPlayer) {
            return;
        }

        $team = $teamPlayer->getTeam();

        if (!$team) {
            return;
        }

        $answer = new QuestionsPhaseAnswer();

        $answer->setUser($user);
        $answer->setTeam($team);
        $answer->setAnswer($userAnswer);

        if ($questionAnswer) {
            $answer->setQuestionsAnswer($questionAnswer);
        }

        if ($phaseQuestion instanceof QuestionsPhaseQuestion) {
            $answer->setPhaseQuestion($phaseQuestion);
        }

        $this->em->persist($answer);
        $this->em->flush();

        $this->gamePlayerTurnService->disablePlayerTurnByUser($game, $user);
        $this->gameActionService->createUserEnteredAnswerAction($game, $team, $user, $userAnswer, $questionAnswer);

        $event = new QuizGameUserEnteredAnswerEvent($game);
        $this->dispatcher->dispatch($event, QuizGameUserEnteredAnswerEvent::NAME);
    }

    private function createPricesAnswer(QuizGame $game, string $userAnswer, User $user, PhaseQuestionInterface $phaseQuestion): void
    {
        $teamPlayer = $game->getTeamPlayerByUser($user);

        if (!$teamPlayer) {
            return;
        }

        $team = $teamPlayer->getTeam();

        if (!$team) {
            return;
        }

        $answer = new PricesPhaseAnswer();

        $answer->setUser($user);
        $answer->setTeam($team);
        $answer->setAnswer($userAnswer);

        if ($phaseQuestion instanceof PricesPhaseQuestion) {
            $answer->setPhaseQuestion($phaseQuestion);
        }

        $this->em->persist($answer);
        $this->em->flush();

        $this->gamePlayerTurnService->disablePlayerTurnByUser($game, $user);
        $this->gameActionService->createUserEnteredAnswerAction($game, $team, $user, $userAnswer);

        $event = new QuizGameUserEnteredAnswerEvent($game);
        $this->dispatcher->dispatch($event, QuizGameUserEnteredAnswerEvent::NAME);
    }

    public function nextStep(QuizGame $game): void
    {
        if ($game->getGameStatus() === QuizGame::GAME_STATUS_CHOOSE_PHASES) {
            $user = $game->getUserWithTurn();

            if ($user) {
                $this->addPhase($game, BasePhase::TYPE_QUESTIONS, $user);
            }

            return;
        }

        $phase = $game->getCurrentPhase();

        if (!$phase) {
            return;
        }

        if (!$phase->isLastQuestion()) {
            $this->startNextQuestion($phase);

            return;
        }

        $this->finishPhase($game);
    }

    private function startNextQuestion(BasePhase $phase): void
    {
        $phase->closeQuestion();
        $this->calculatePoints($phase);
        $game = $phase->getGame();
        $this->refreshGameLastAction($game);

        $this->em->flush();

        $this->gamePlayerTurnService->updatePlayersTurnInEveryTeam($game);
        $this->gameActionService->createNewQuestionInProgressAction($game);
    }

    private function finishPhase(QuizGame $game): void
    {
        $phase = $game->getCurrentPhase();

        if (!$phase) {
            return;
        }

        $phase->closeQuestion();
        $this->calculatePoints($phase);
        $game->finishCurrentPhase();
        $this->refreshGameLastAction($game);
        $this->em->flush();

        $this->gamePlayerTurnService->updatePlayersTurnInEveryTeam($game);

        $event = new QuizGamePhaseFinishedEvent($game, $phase);
        $this->dispatcher->dispatch($event, QuizGamePhaseFinishedEvent::NAME);

        $this->gameActionService->createQuizGamePhaseFinishedAction($game, $phase);
    }

    private function refreshGameLastAction(?QuizGame $game): void
    {
        if (!$game) {
            return;
        }

        $game->refreshLastAction();
        $this->em->flush();

        $command = QuizNextStepCommand::getDefaultName() . ' --game_id=' . $game->getId() . ' --timestamp=' . (time() + 1);
        $this->consoleCommandService->addCommandToQueueWithDelay20S($command);
    }

    private function calculatePoints(BasePhase $phase): void
    {
        $phaseQuestions = $phase->getAnsweredPhaseQuestions();

        foreach ($phaseQuestions as $phaseQuestion) {
            $this->calculatePointsForQuestion($phase, $phaseQuestion);
        }
    }

    private function calculatePointsForQuestion(BasePhase $phase, PhaseQuestionInterface $phaseQuestion): void
    {
        $phaseAnswers = $phaseQuestion->getPhaseAnswers();

        if ($phase instanceof QuestionsPhase) {
            foreach ($phaseAnswers as $phaseAnswer) {
                /** @var QuestionsPhaseAnswer $phaseAnswer */
                if ($phaseAnswer->isCorrect()) {
                    $team = $phaseAnswer->getTeam();

                    if (!$team) {
                        continue;
                    }

                    $team->setPoints($team->getPoints() + 1);
                }
            }
        }

        if ($phase instanceof PricesPhase && $phaseQuestion instanceof PricesPhaseQuestion) {
            /** @var PricesAnswer $answer */
            $question = $phaseQuestion->getQuestion();

            if (!$question) {
                return;
            }

            $answer = $question->getAnswers()->first();
            $teams = [];

            foreach ($phaseAnswers as $key => $phaseAnswer) {
                /* @var PricesPhaseAnswer $phaseAnswer */
                $teams[$key] = [
                    'team' => $phaseAnswer->getTeam(),
                    'value' => abs(intval($phaseAnswer->getAnswer()) - $answer->getIntAnswer())
                ];
            }

            if (empty($teams)) {
                return;
            }

            if (!isset($teams[0])) {
                return;
            }

            if (count($teams) === 1) {
                $team = $teams[0]['team'];
                $team->setPoints($team->getPoints() + 1);
            } elseif ($teams[0]['value'] < $teams[1]['value']) {
                $team = $teams[0]['team'];
                $team->setPoints($team->getPoints() + 1);
            } elseif ($teams[0]['value'] > $teams[1]['value']) {
                $team = $teams[1]['team'];
                $team->setPoints($team->getPoints() + 1);
            } else {
                foreach ($teams as $team) {
                    $team = $team['team'];
                    $team->setPoints($team->getPoints() + 1);
                }
            }
        }

        $phaseQuestion->setStatus(PhaseQuestionInterface::STATUS_COUNTED);

        $this->em->flush();
        $this->gameActionService->createGamePointsChangedAction($phase->getGame());
    }
}
