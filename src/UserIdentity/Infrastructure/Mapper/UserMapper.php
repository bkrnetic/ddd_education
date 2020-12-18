<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Mapper;

use Storage\Doctrine\Main\Entity\User as StorageUser;
use UserIdentity\Application\Model\User;

class UserMapper
{
    public function mapToDomainObject(StorageUser $storageUser): User
    {
        return new User(
            $storageUser->getId(),
            $storageUser->getUsername(),
            $storageUser->getEmail(),
            $storageUser->getName()
        );
    }
}
