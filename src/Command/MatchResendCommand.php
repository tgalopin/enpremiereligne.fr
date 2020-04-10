<?php

namespace App\Command;

use App\Entity\HelpRequest;
use App\Model\MatchedNeeds;
use App\Repository\HelpRequestRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MatchResendCommand extends Command
{
    protected static $defaultName = 'app:match:resend';

    private HelpRequestRepository $repository;
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private string $sender;

    public function __construct(HelpRequestRepository $repository, MailerInterface $mailer, TranslatorInterface $translator, string $sender)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->sender = $sender;
    }

    protected function configure()
    {
        $this
            ->setDescription('Resend the match e-mail when necessary.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not send the emails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $history = $this->repository->findNeedsByOwner(['finished' => true], ['createdAt' => 'DESC']);

        foreach ($history as $owner) {
            $types = [];
            foreach ($owner as $request) {
                $types[$request->helpType][] = $request;
            }

            /** @var HelpRequest[] $requests */
            foreach ($types as $type => $requests) {
                if ($requests[0]->getCreatedAt() < new \DateTime('2020-04-01 00:00')) {
                    continue;
                }

                $template = $type;
                if ('vulnerable' === $requests[0]->jobType) {
                    $template = 'vulnerable_'.($requests[0]->ccEmail ? 'other' : 'self');
                }

                $to = [$requests[0]->email, $requests[0]->matchedWith->email];
                if ($requests[0]->ccEmail) {
                    $to[] = $requests[0]->ccEmail;
                }

                $email = (new TemplatedEmail())
                    ->from($this->sender)
                    ->to(...$to)
                    ->subject($this->translator->trans('email.match-subject'))
                    ->htmlTemplate('emails/match_'.$template.'.html.twig')
                    ->context([
                        'requester' => $requests[0],
                        'needs' => new MatchedNeeds($requests),
                        'helper' => $requests[0]->matchedWith,
                    ])
                ;

                $output->writeln(sprintf(
                    'Sending email to %s for request from %s',
                    implode(', ', $to),
                    $requests[0]->getCreatedAt()->format('Y-m-d H:i:s')
                ));

                if (!$input->getOption('dry-run')) {
                    $this->mailer->send($email);
                }
            }
        }

        return 0;
    }
}
