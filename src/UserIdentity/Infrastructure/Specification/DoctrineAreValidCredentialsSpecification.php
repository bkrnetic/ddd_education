<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Specification;

use Storage\Doctrine\Main\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UserIdentity\Application\Specification\AreValidCredentialsSpecification;

final class DoctrineAreValidCredentialsSpecification implements AreValidCredentialsSpecification
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private UserRepository $userRepository;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function satisfiedBy(string $email, string $password): bool
    {
        $user = $this->userRepository->findOneByEmail($email);

        if (!$user) {
            return false;
        }

        return $this->passwordEncoder->isPasswordValid($user, $password);
    }
}
