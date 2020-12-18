<?php

declare(strict_types=1);

namespace UserIdentity\Application\Manager;

interface TokenManager
{
    public function createToken(string $email): ?string;
}
