<?php

declare(strict_types=1);

namespace UserIdentity\Application\Specification;

interface IsExistingEmailSpecification
{
    public function satisfiedBy(string $email): bool;
}
