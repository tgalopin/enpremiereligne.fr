<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminCreateCommand extends Command
{
    protected static $defaultName = 'app:admin:create';

    private EntityManagerInterface $entityManager;

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new administrator for the site.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username to be created.')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password for the account. '.
                'If you don\'t include this argument you\'ll be prompted to enter it.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if (empty($password = $input->getArgument('password'))) {
                $password = $this->getUserPassword($input, $output);
            }
            $username = $this->verifyUserDoesntExist($input->getArgument('username'));

            $user = new Admin();
            $user->username = $username;
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $password)
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success(
                sprintf('User %s has been created', $username)
            );
        } catch (Exception $exception) {
            $io->error($exception->getMessage());
        }

        return 0;
    }

    private function getUserPassword(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question('Enter the user\'s password:');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);

        $question = new Question('Repeat the password please:');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $confirm = $helper->ask($input, $output, $question);

        if ($password !== $confirm) {
            throw new RuntimeException('The passwords provided don\'t match. Please try again.');
        }

        return $password;
    }

    private function verifyUserDoesntExist(string $username): string
    {
        $existing = $this->entityManager->getRepository(Admin::class)
            ->findOneBy(['username' => $username]);

        if (null === $existing) {
            return $username;
        }

        throw new RuntimeException(sprintf('A user with the username %s already exists', $username));
    }
}
