<?php

declare(strict_types=1);

namespace Common\Application\Specification;

interface CategoryExistsSpecification
{
    public function satisfiedBy(string $uuid): bool;
}
