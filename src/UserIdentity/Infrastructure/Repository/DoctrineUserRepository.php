<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Repository;

use Storage\Doctrine\Main\Repository\UserRepository as StorageDoctrineUserRepository;
use UserIdentity\Application\Model\User;
use UserIdentity\Application\Repository\UserRepository;
use UserIdentity\Infrastructure\Mapper\UserMapper;

class DoctrineUserRepository implements UserRepository
{
    private StorageDoctrineUserRepository $userRepository;
    private UserMapper $userMapper;

    public function __construct(
        StorageDoctrineUserRepository $userRepository,
        UserMapper $userMapper
    ) {
        $this->userRepository = $userRepository;
        $this->userMapper = $userMapper;
    }

    public function getByEmail(string $email): ?User
    {
        $storageUser = $this->userRepository->findOneByEmail($email);

        if (!$storageUser) {
            return null;
        }

        return $this->userMapper->mapToDomainObject($storageUser);
    }
}
