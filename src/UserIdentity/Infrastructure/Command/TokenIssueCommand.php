<?php

namespace UserIdentity\Infrastructure\Command;

use Storage\Doctrine\Main\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UserIdentity\Application\Manager\TokenManager;

class TokenIssueCommand extends Command
{
    private UserRepository $userRepository;
    private TokenManager $tokenManager;

    public function __construct(
        UserRepository $userRepository,
        TokenManager $tokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->tokenManager = $tokenManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:token:issue')
            ->setDescription('Issue a token for user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Creating a new token key');

        $email = $io->ask('E-mail:');

        $token = $this->tokenManager->createToken($email);

        if (null === $token) {
            $io->error(sprintf('User with email %s doesn\'t exist', $email));

            return 1;
        }

        $io->success('Token successfully generated');

        $io->block(
            $token,
            null,
            'fg=black;bg=yellow',
            ' ',
            true
        );

        return 0;
    }
}
