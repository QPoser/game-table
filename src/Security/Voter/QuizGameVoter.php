<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\QuizGame;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuizGameVoter extends Voter
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
                return $subject->hasUser($user) && $subject->getStatus() === Game::STATUS_STARTED && $subject->getGameStatus() === QuizGame::GAME_STATUS_CHOOSE_PHASES && $subject->isUserTurn($user);
                break;
            case self::ATTRIBUTE_PUT_ANSWER:
                return $subject->hasUser($user) && $subject->getStatus() === Game::STATUS_STARTED && $subject->getGameStatus() === QuizGame::GAME_STATUS_PLAYING && $subject->isUserTurn($user);
                break;
        }

        return false;
    }
}
