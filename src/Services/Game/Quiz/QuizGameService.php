<?php
declare(strict_types=1);

namespace App\Services\Game\Quiz;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\PhaseQuestionInterface;
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
use App\Services\Game\GameActionService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class QuizGameService
{
    private EntityManagerInterface $em;

    private ValidationService $validator;

    private GameNotificationTemplateHelper $gameNTH;

    private GameActionService $gameActionService;

    private QuizPhaseService $quizPhaseService;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        ValidationService $validator,
        GameNotificationTemplateHelper $gameNTH,
        GameActionService $gameActionService,
        QuizPhaseService $quizPhaseService,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->gameNTH = $gameNTH;
        $this->gameActionService = $gameActionService;
        $this->quizPhaseService = $quizPhaseService;
        $this->dispatcher = $dispatcher;
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
        $game->setFirstTeamPlayerTurn();

        $this->em->persist($game);
        $this->em->flush($game);

        $this->quizPhaseService->createPhase(BasePhase::TYPE_QUESTIONS, $game);

        $this->gameActionService->createGameStartedActions($game);
        $this->gameNTH->createGameStartedNotifications($game);
    }

    public function addPhase(QuizGame $game, string $phaseType, User $user): void
    {
        $this->quizPhaseService->createPhase($phaseType, $game, $user);

        $this->gameActionService->createQuizGamePhaseChosenActions($game, $user, $phaseType);

        if ($game->getPhases()->count() === QuizGame::PHASES_COUNT) {
            $this->startGamePlaying($game);
        } else {
            $game->setSecondTeamPlayerTurn();
            $this->em->flush();
            $this->gameActionService->createGameTurnsChangedAction($game);
        }
    }

    public function startGamePlaying(QuizGame $game): void
    {
        $game->setGameStatus(QuizGame::GAME_STATUS_PLAYING);
        $game->setPlayersTurnInEveryTeam(0);

        /** @var BasePhase $phase */
        $phase = $game->getPreparedPhase();
        $phase->play();

        $this->em->flush();

        $this->gameActionService->createQuizGamePlayingActions($game);
        $this->gameActionService->createGameTurnsChangedAction($game);
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

        switch ($currentPhase->getType()) {
            case BasePhase::TYPE_QUESTIONS:
                    if (!($answer instanceof QuestionsAnswer)) {
                        $answer = null;
                    }

                    $this->createQuestionsAnswer($game, $userAnswer, $user, $currentPhase->getCurrentPhaseQuestion(), $answer);
                break;
        }
    }

    private function createQuestionsAnswer(QuizGame $game, string $userAnswer, User $user, PhaseQuestionInterface $phaseQuestion, ?QuestionsAnswer $questionAnswer): void
    {
        $team = $game->getTeamPlayerByUser($user)->getTeam();

        $answer = new QuestionsPhaseAnswer();

        $answer->setUser($user);
        $answer->setTeam($team);
        $answer->setQuestionsAnswer($questionAnswer);
        $answer->setAnswer($userAnswer);

        if ($phaseQuestion instanceof QuestionsPhaseQuestion) {
            $answer->setPhaseQuestion($phaseQuestion);
        }

        $game->disablePlayerTurnByUser($user);

        $this->em->persist($answer);
        $this->em->flush();

        $this->gameActionService->createUserEnteredAnswerAction($game, $team, $user, $userAnswer, $questionAnswer);
        $this->gameActionService->createGameTurnsChangedAction($game);

        $event = new QuizGameUserEnteredAnswerEvent($game);
        $this->dispatcher->dispatch($event, QuizGameUserEnteredAnswerEvent::NAME);
    }

    public function startNextQuestion(BasePhase $phase): void
    {
        $phase->closeQuestion();

        $this->em->flush();

        $this->gameActionService->createNewQuestionInProgressAction($phase->getGame());
    }

    public function finishPhase(QuizGame $game): void
    {
        $phase = $game->getCurrentPhase();

        if (!$phase) {
            return;
        }

        $phase->closeQuestion();

        if ($phase->isAllQuestionsFinished()) {
            $phase->setStatus(BasePhase::STATUS_FINISHED);
        }

        $game->finishCurrentPhase();

        $this->em->flush();

        $event = new QuizGamePhaseFinishedEvent($game, $phase);
        $this->dispatcher->dispatch($event, QuizGamePhaseFinishedEvent::NAME);

        $this->gameActionService->createQuizGamePhaseFinishedAction($game, $phase);
    }
}