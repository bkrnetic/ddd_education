<?php

declare(strict_types=1);

namespace UserIdentity\Application\Repository;

use UserIdentity\Application\Model\User;

interface UserRepository
{
    public function getByEmail(string $email): ?User;
}
