<?php

declare(strict_types=1);

namespace Storage\Doctrine\Main\Repository;

use Storage\Doctrine\Main\Entity\User;

class UserRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = User::class;

    protected function getEntityClassName(): string
    {
        return self::ENTITY_CLASS_NAME;
    }

    public function findOneByUsername(string $username): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['username' => $username]);

        return $user;
    }

    public function findOneByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['email' => $email]);

        return $user;
    }
}
