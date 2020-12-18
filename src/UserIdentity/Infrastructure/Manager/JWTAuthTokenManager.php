<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Manager;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Storage\Doctrine\Main\Repository\UserRepository;
use UserIdentity\Application\Manager\TokenManager;

class JWTAuthTokenManager implements TokenManager
{
    private UserRepository $userRepository;

    private JWTTokenManagerInterface $tokenManager;

    public function __construct(
        UserRepository $userRepository,
        JWTTokenManagerInterface $tokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;
    }

    public function createToken(string $email): ?string
    {
        $user = $this->userRepository->findOneByEmail($email);

        if (null === $user) {
            return null;
        }

        return $this->tokenManager->create($user);
    }
}
