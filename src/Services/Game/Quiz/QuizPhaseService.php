<?php
declare(strict_types=1);

namespace App\Services\Game\Quiz;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\Questions\Question;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhase;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseQuestion;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\Game\Team\GameTeam;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Game\GameActionService;
use App\Services\Notification\GameNotificationTemplateHelper;
use App\Services\Response\ErrorCode;
use App\Services\Validation\ValidationService;
use Doctrine\ORM\EntityManagerInterface;

class QuizPhaseService
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    public function createPhase(string $type, QuizGame $game): BasePhase
    {
        if (!in_array($type, BasePhase::AVAILABLE_TYPES, true)) {
            throw new AppException(ErrorCode::USER_ALREADY_HAS_GAME_IN_PROGRESS);
        }

        $phase = null;

        switch ($type) {
            case BasePhase::TYPE_QUESTIONS:
                    $phase = $this->createQuestionsPhase();
                break;
        }

        /** @var BasePhase $phase */

        $phase->setGame($game);
        $game->addPhase($phase);
        $phase->setStatus(BasePhase::STATUS_PREPARED);

        $this->em->persist($phase);
        $this->em->flush();

        return $phase;
    }

    private function createQuestionsPhase(): QuestionsPhase
    {
        $phase = new QuestionsPhase();

        $questions = $this->em->getRepository(Question::class)->findBy([], [], 3, 0);

        foreach ($questions as $question) {
            /** @var Question $question */
            $phaseQuestion = new QuestionsPhaseQuestion();
            $phaseQuestion->setStatus(QuestionsPhaseQuestion::STATUS_WAIT);
            $phaseQuestion->setQuestion($question);

            $phase->addQuestion($phaseQuestion);

            $this->em->persist($phaseQuestion);
        }

        return $phase;
    }
}