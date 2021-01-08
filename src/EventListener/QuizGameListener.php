<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseQuestion;
use App\Entity\Game\Quiz\QuizGame;
use App\Events\GameFinishedEvent;
use App\Events\QuizGamePhaseFinishedEvent;
use App\Events\QuizGameUserEnteredAnswerEvent;
use App\Services\Game\GameActionService;
use App\Services\Game\GameService;
use App\Services\Game\Quiz\QuizGameService;

final class QuizGameListener
{
    private GameService $gameService;

    private GameActionService $gameActionService;

    private QuizGameService $quizGameService;

    public function __construct(
        GameService $gameService,
        GameActionService $gameActionService,
        QuizGameService $quizGameService
    ) {
        $this->gameService = $gameService;
        $this->gameActionService = $gameActionService;
        $this->quizGameService = $quizGameService;
    }

    public function onUserEnteredAnswer(QuizGameUserEnteredAnswerEvent $event): void
    {
        $game = $event->getGame();
        $phase = $game->getCurrentPhase();

        /** @var QuestionsPhaseQuestion $question */
        $question = $phase->getCurrentPhaseQuestion();

        if ($question->getPhaseAnswers()->count() < $game->getTeams()->count()) {
            return;
        }

        $this->quizGameService->nextStep($game);
    }

    public function onPhaseFinished(QuizGamePhaseFinishedEvent $event): void
    {
        $game = $event->getGame();

        if ($game->isAllPhasesFinished()) {
            $this->gameService->finishGame($game);
        } else {
            $this->quizGameService->nextStep($game);
        }
    }

    public function onGameFinished(GameFinishedEvent $event): void
    {
        $game = $event->getGame();

        if ($game instanceof QuizGame) {
            $this->gameActionService->createQuizGameFinishedAction($game);
        }
    }
}
