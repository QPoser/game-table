<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Game\Game;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GameVoter extends Voter
{
    const ATTRIBUTE_VISIT = 'VISIT';
    const ATTRIBUTE_JOIN = 'JOIN';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ATTRIBUTE_VISIT, self::ATTRIBUTE_JOIN], true) && $subject instanceof Game;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || !$subject instanceof Game) {
            return false;
        }

        switch ($attribute) {
            case self::ATTRIBUTE_VISIT:
                return $subject->hasUser($user);
                break;
            case self::ATTRIBUTE_JOIN:
                return $subject->canUserJoin($user);
                break;
        }

        return false;
    }
}
