<?php

declare(strict_types=1);

namespace App\Services\Game\Quiz;

use App\Entity\Game\Quiz\Phase\BasePhase;
use App\Entity\Game\Quiz\Phase\Prices\PricesPhase;
use App\Entity\Game\Quiz\Phase\Prices\PricesPhaseQuestion;
use App\Entity\Game\Quiz\Phase\Prices\PricesQuestion;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhase;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsPhaseQuestion;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsQuestion;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\User;
use App\Exception\AppException;
use App\Services\Response\ErrorCode;
use Doctrine\ORM\EntityManagerInterface;

final class QuizPhaseService
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    public function createPhase(string $type, QuizGame $game, ?User $user = null): BasePhase
    {
        if (!in_array($type, BasePhase::AVAILABLE_TYPES, true)) {
            throw new AppException(ErrorCode::QUIZ_GAME_PHASE_DOES_NOT_EXISTS);
        }

        if ($user && in_array($type, BasePhase::VIP_TYPES, true) && !$user->isVip()) {
            throw new AppException(ErrorCode::QUIZ_GAME_PHASE_ONLY_FOR_VIP_USERS);
        }

        $phase = null;

        switch ($type) {
            case BasePhase::TYPE_QUESTIONS:
                    $phase = $this->createQuestionsPhase();
                break;
            case BasePhase::TYPE_PRICES:
                $phase = $this->createPricesPhase();
                break;
        }

        /* @var BasePhase $phase */
        $phase->setUser($user);
        $game->addPhase($phase);
        $phase->setStatus(BasePhase::STATUS_PREPARED);

        $this->em->persist($phase);
        $this->em->flush();

        return $phase;
    }

    private function createQuestionsPhase(): QuestionsPhase
    {
        $phase = new QuestionsPhase();

        $questions = $this->em->getRepository(QuestionsQuestion::class)->findBy([], [], 3, 0);

        foreach ($questions as $question) {
            /** @var QuestionsQuestion $question */
            $phaseQuestion = new QuestionsPhaseQuestion();
            $phaseQuestion->setStatus(QuestionsPhaseQuestion::STATUS_WAIT);
            $phaseQuestion->setQuestion($question);

            $phase->addQuestion($phaseQuestion);

            $this->em->persist($phaseQuestion);
        }

        return $phase;
    }

    private function createPricesPhase(): PricesPhase
    {
        $phase = new PricesPhase();

        $questions = $this->em->getRepository(PricesQuestion::class)->findBy([], [], 3, 0);

        foreach ($questions as $question) {
            /** @var PricesQuestion $question */
            $phaseQuestion = new PricesPhaseQuestion();
            $phaseQuestion->setStatus(PricesPhaseQuestion::STATUS_WAIT);
            $phaseQuestion->setQuestion($question);

            $phase->addQuestion($phaseQuestion);

            $this->em->persist($phaseQuestion);
        }

        return $phase;
    }
}
