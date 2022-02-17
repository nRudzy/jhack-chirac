<?php

namespace App\Command;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-emails',
    description: 'Send emails',
)]
class SendEmailsCommand extends Command
{
    public function __construct(private MailerInterface $mailer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('campaign', InputArgument::REQUIRED, 'Filename of the mail (without .html.twig, eg: sesame)')
            ->addArgument('emailsFilename', InputArgument::REQUIRED, 'Filename of the emails (without .txt, eg: salve1)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $campaign = $input->getArgument('campaign');
        $emailsFilename = $input->getArgument('emailsFilename');
        $nbOfEmailSent = 0;

        foreach (preg_split("/\n/", file_get_contents(sprintf('%s.txt', $emailsFilename))) as $emailTo) {
            $email = (new TemplatedEmail())
                ->from(new Address('sesame.linkvalue@gmail.com', 'Sésame'))
                ->to($emailTo)
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Notification Sésame : Demande de feedback')
                ->htmlTemplate(sprintf('mail/%s.html.twig', $campaign));

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $io->error(sprintf('Error on sending email to %s : %s', $emailTo, $e->getMessage()));
                $io->info(sprintf('%d emails sent from %s.txt file', $nbOfEmailSent, $emailsFilename));

                return Command::FAILURE;
            }

            $io->note(sprintf('Phishing email to %s sent', $emailTo));
            ++$nbOfEmailSent;

            sleep(15);
        }

        $io->success(sprintf('%d emails successfully sent from %s.txt file', $nbOfEmailSent, $emailsFilename));

        return Command::SUCCESS;
    }
}
