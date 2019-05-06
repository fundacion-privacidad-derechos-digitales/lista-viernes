<?php

namespace App\Command;

use App\Entity\Email;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddAccountEmailToEmailsTableCommand extends Command
{
    protected static $defaultName = 'app:migrate:account-email-to-emails';

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Add the account email to the emails table if not exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $count = 1;

        foreach ($users as $user) {
            /** @var User $user */

            $existAccountEmail = false;

            foreach ($user->getEmails() as $email) {
                if ($email->getEmail() === $user->getEmail()) {
                    $existAccountEmail = true;
                }
            }

            if ($existAccountEmail === false) {
                $accountEmail = new Email();
                $accountEmail->setEmail($user->getEmail());
                $accountEmail->setValidated(true);
                $accountEmail->setValidationToken($user->getEmailValidationToken());

                $user->addEmail($accountEmail);

                $this->em->persist($user);
                $this->em->persist($accountEmail);
                $this->em->flush();

                $output->writeln($count . ' Email ' . $user->getEmail() . ' successfully added!');
            } else {
                $output->writeln($count . ' Email ' . $user->getEmail() . ' already in emails');
            }

            $count++;
        }
    }
}
