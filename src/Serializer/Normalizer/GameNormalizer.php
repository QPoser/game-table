<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Game\Game;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class GameNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private ObjectNormalizer $normalizer;

    private TokenStorageInterface $tokenStorage;

    public function __construct(ObjectNormalizer $normalizer, TokenStorageInterface $tokenStorage)
    {
        $this->normalizer = $normalizer;
        $this->tokenStorage = $tokenStorage;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Game $object */
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;
        $user = $user instanceof User ? $user : null;

        if ($user && $object->hasUser($user)) {
            $object->setUserInGame(true);
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Game;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
