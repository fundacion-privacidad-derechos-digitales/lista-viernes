<?php

namespace App\Command;

use App\Entity\Email;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeEmailCommand extends Command
{
    protected static $defaultName = 'app:normalize:email';

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Normalize the Email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emails = $this->em->getRepository(Email::class)->findAll();

        $count = 1;

        foreach ($emails as $email) {
            $oldEmail = $email->getEmail();

            $email->setEmail($oldEmail);

            if ($oldEmail !== $email->getEmail()) {
                $this->em->persist($email);
                $this->em->flush();
                $output->writeln($count . ' Email ' . $email->getEmail() . ' successfully normalized!');
            } else {
                $output->writeln($count . ' Email ' . $email->getEmail() . ' normalization not necessary');
            }

            $count++;
        }
    }
}