<?php

declare(strict_types=1);

namespace UserIdentity\Application\Specification;

interface AreValidCredentialsSpecification
{
    public function satisfiedBy(string $email, string $password): bool;
}
