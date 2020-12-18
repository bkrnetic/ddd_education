<?php

namespace UserIdentity\Infrastructure\Manager;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Storage\Doctrine\Main\Entity\User;
use Storage\Doctrine\Main\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private UserRepository $repository;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    /**
     * @param array<mixed, mixed> $data
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(array $data): User
    {
        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setRoles([$data['roles']]);
        $user->setName($data['name']);
        $user->setIsActive($data['isActive']);

        $hashedPassword = $this->encoder->encodePassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->repository->save($user);

        return $user;
    }

    /**
     * @param array<mixed, mixed> $data
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(User $user, array $data): User
    {
        if (\array_key_exists('username', $data)) {
            $user->setUsername($data['username']);
        }
        if (\array_key_exists('email', $data)) {
            $user->setEmail($data['email']);
        }
        if (\array_key_exists('role', $data)) {
            $user->setRoles([$data['role']]);
        }
        if (\array_key_exists('name', $data)) {
            $user->setName($data['name']);
        }
        if (\array_key_exists('isActive', $data)) {
            $user->setIsActive($data['isActive']);
        }

        $this->repository->save($user);

        return $user;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $user): void
    {
        $this->repository->remove($user);
    }
}
