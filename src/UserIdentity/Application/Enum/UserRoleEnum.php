<?php

declare(strict_types=1);

namespace UserIdentity\Application\Enum;

use Common\Domain\Enum\BaseEnum;

class UserRoleEnum extends BaseEnum
{
    const DEFAULT_ROLE = self::ROLE_USER;
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';

    const ALLOWED_RULES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];
}
