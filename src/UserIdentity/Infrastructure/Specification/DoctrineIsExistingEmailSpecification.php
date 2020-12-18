<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Specification;

use Storage\Doctrine\Main\Repository\UserRepository;
use UserIdentity\Application\Specification\IsExistingEmailSpecification;

final class DoctrineIsExistingEmailSpecification implements IsExistingEmailSpecification
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function satisfiedBy(string $email): bool
    {
        return $this->userRepository->findOneByEmail($email) !== null;
    }
}
