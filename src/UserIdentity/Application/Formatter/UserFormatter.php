<?php

declare(strict_types=1);

namespace UserIdentity\Application\Formatter;

use UserIdentity\Application\Model\User;

final class UserFormatter
{
    /** @return array<string, mixed> */
    public function format(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
        ];
    }
}
