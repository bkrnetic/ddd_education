<?php

namespace Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Storage\Doctrine\Main\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UserIdentity\Application\Enum\UserRoleEnum;

final class UsersFixture implements FixtureInterface
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->createUserObject(
            'admin',
            'admin@mailinator.com',
            UserRoleEnum::ROLE_ADMIN,
            'Administrator',
        );
        $manager->persist($admin);

        $manager->flush();
    }

    private function createUserObject(
        string $username,
        string $email,
        string $role,
        string $name,
        string $password = '123456',
        bool $isActive = true
    ): User {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setName($name);
        $user->setIsActive($isActive);

        $password = $this->encoder->encodePassword($user, $password);
        $user->setPassword($password);

        return $user;
    }
}
