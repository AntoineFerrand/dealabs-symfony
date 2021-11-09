<?php

namespace App\Command;

use App\Entity\Account;
use App\Entity\Alerte;
use App\Entity\Deals;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;

class SendNotifCommand extends Command
{
    protected static $defaultName = 'send-notif';
    protected static $description = 'Envoie un mail aux user selon leurs alertes';
    protected  $mailer;
    protected  $accountRepository;
    protected  $alerteRepository;
    protected  $dealsRepository;

    public function __construct( MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->accountRepository = $entityManager->getRepository(Account::class);
        $this->alerteRepository = $entityManager->getRepository(Alerte::class);
        $this->dealsRepository = $entityManager->getRepository(Deals::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$description)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->accountRepository->findAll();
        foreach ($users as $user) {
            $alertes = $this->alerteRepository->findBy(
                ['creator' => $user]
            );
            $deals = array();
            foreach ($alertes as $alerte){
                if ($alerte->getNotification()){
                    $ajout = $this->dealsRepository->findOneByKeyWord($alerte->getMotscles());
                    if ($ajout !== null){
                        array_push($deals, $ajout);
                    }
                }

            }
            if (!empty($deals)){
                $email = new TemplatedEmail();
                $email->from(new Address("contact@mail.com", "Dealabs"))
                    ->to($user->getEmail())
                    ->htmlTemplate('bon_plan/notif-email.html.twig')
                    ->context([
                        'deals' => $deals,
                    ])
                    ->subject("Nouveaux articles en lien avec vos alertes!");
                $this->mailer->send($email);
            }
        }

        $io->success('Les mails ont bien été envoyé.');

        return 0;
    }
}
