<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class QuizGameVoter extends Voter
{
    public const ATTRIBUTE_SELECT_PHASE = 'SELECT_PHASE';
    public const ATTRIBUTE_PUT_ANSWER = 'PUT_ANSWER';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ATTRIBUTE_SELECT_PHASE, self::ATTRIBUTE_PUT_ANSWER], true) && $subject instanceof QuizGame;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || !$subject instanceof QuizGame) {
            return false;
        }

        switch ($attribute) {
            case self::ATTRIBUTE_SELECT_PHASE:
                return $this->canUserSelectPhase($user, $subject);
                break;
            case self::ATTRIBUTE_PUT_ANSWER:
                return $this->canUserPutAnswer($user, $subject);
                break;
        }

        return false;
    }

    private function canUserPutAnswer(User $user, QuizGame $subject): bool
    {
        if (!$subject->hasUser($user)) {
            return false;
        }

        if ($subject->getStatus() !== Game::STATUS_STARTED) {
            return false;
        }

        if ($subject->getGameStatus() !== QuizGame::GAME_STATUS_PLAYING) {
            return false;
        }

        if (!$subject->isUserTurn($user)) {
            return false;
        }

        if ($subject->getCurrentStepSeconds() === null) {
            return false;
        }

        return true;
    }

    private function canUserSelectPhase(User $user, QuizGame $subject): bool
    {
        if (!$subject->hasUser($user)) {
            return false;
        }

        if ($subject->getStatus() !== Game::STATUS_STARTED) {
            return false;
        }

        if ($subject->getGameStatus() !== QuizGame::GAME_STATUS_CHOOSE_PHASES) {
            return false;
        }

        if (!$subject->isUserTurn($user)) {
            return false;
        }

        if ($subject->getCurrentStepSeconds() === null) {
            return false;
        }

        return true;
    }
}
