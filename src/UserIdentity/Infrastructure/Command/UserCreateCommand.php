<?php

namespace UserIdentity\Infrastructure\Command;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use UserIdentity\Application\Enum\UserRoleEnum;
use UserIdentity\Infrastructure\Manager\UserManager;

class UserCreateCommand extends Command
{
    private UserManager $manager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private SerializerInterface $serializer;

    public function __construct(
        UserManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        SerializerInterface $serializer
    ) {
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:user:create')
            ->setDescription('Create a new base user');
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Create new user');

        $username = $io->ask('Username:');
        $email = $io->ask('E-mail:');
        $password = $io->ask('Password:');
        $name = $io->ask('Name:');
        $role = $io->choice('Role:', UserRoleEnum::ALLOWED_RULES, UserRoleEnum::DEFAULT_ROLE);
        $isActive = $io->confirm('Do you wish to activate the user?');

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'roles' => $role,
            'active' => $isActive,
            'isActive' => $isActive,
        ];

        $user = $this->manager->create($data);

        // get JSON, convert to array, make it pretty (not the most elegant solution, but it's just a helper)
        $userJson = $this->serializer->serialize($user, 'json');
        $userDataArray = json_decode($userJson, true, 512, JSON_THROW_ON_ERROR);
        $userJsonPretty = json_encode($userDataArray, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT, 512);

        $io->writeln($userJsonPretty);
        $io->success('User successfully created');

        return 0;
    }
}
