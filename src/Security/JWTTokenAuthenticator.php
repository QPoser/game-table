<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

final class JWTTokenAuthenticator extends BaseAuthenticator
{
    public function checkCredentials($credentials, UserInterface $user)
    {
        /** @var User $user */
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException('User is not active. Check your email.');
        }

        return parent::checkCredentials($credentials, $user);
    }
}
