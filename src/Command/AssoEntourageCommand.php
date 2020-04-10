<?php

namespace App\Command;

use App\Repository\HelperRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;

class AssoEntourageCommand extends Command
{
    protected static $defaultName = 'app:asso:entourage';

    private HelperRepository $repository;
    private MailerInterface $mailer;

    public function __construct(HelperRepository $repository, MailerInterface $mailer)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send an e-mail about Entourage need.')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Only send a test e-mail to team@enpremiereligne.fr')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not send the emails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('test')) {
            $helpers = [['firstName' => 'Titouan', 'email' => 'team@enpremiereligne.fr']];
        } else {
            $helpers = $this->repository->findAssoEntourage();
        }

        $count = 0;

        foreach ($helpers as $helper) {
            $email = (new TemplatedEmail())
                ->from('team@enpremiereligne.fr')
                ->to($helper['email'])
                ->subject('[En Première Ligne] Entourage on a besoin de vous ! ☎️ passez des coups de fil aux sans domicile !')
                ->htmlTemplate('emails/fr_FR/asso_entourage.html.twig')
                ->context(['helper' => $helper])
            ;

            $output->writeln('Sending e-mail to '.$helper['email']);

            if (!$input->getOption('dry-run')) {
                $this->mailer->send($email);
            }

            ++$count;
        }

        $output->writeln($count.' emails sent');

        return 0;
    }
}
