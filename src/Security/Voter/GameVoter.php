<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Game\Game;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class GameVoter extends Voter
{
    public const ATTRIBUTE_VISIT = 'VISIT';
    public const ATTRIBUTE_JOIN = 'JOIN';
    public const ATTRIBUTE_LEAVE = 'LEAVE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ATTRIBUTE_VISIT, self::ATTRIBUTE_JOIN, self::ATTRIBUTE_LEAVE], true) && $subject instanceof Game;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || !$subject instanceof Game) {
            return false;
        }

        switch ($attribute) {
            case self::ATTRIBUTE_LEAVE:
                return $subject->hasUser($user) && $subject->getStatus() === Game::STATUS_CREATED;
                break;
            case self::ATTRIBUTE_VISIT:
                return $subject->hasUser($user) && $subject->getStatus() === Game::STATUS_STARTED;
                break;
            case self::ATTRIBUTE_JOIN:
                return $subject->canUserJoin($user);
                break;
        }

        return false;
    }
}
