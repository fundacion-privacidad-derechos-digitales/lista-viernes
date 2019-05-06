<?php

namespace App\Command;

use App\Encrypt\EncryptionService;
use App\Entity\Email;
use App\Hash\HashService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptEmailsCommand extends Command
{
    protected static $defaultName = 'app:encrypt:emails';

    private $em;
    private $encryptionService;
    private $hashService;

    public function __construct(ObjectManager $em, EncryptionService $encryptionService, HashService $hashService)
    {
        $this->em = $em;
        $this->encryptionService = $encryptionService;
        $this->hashService = $hashService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Encrypt the Email table');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emails = $this->em->getRepository(Email::class)->findAll();

        $count = 1;

        foreach ($emails as $email) {
            $email->setEmail($email->getEmail());
            $this->encryptionService->encrypt($email);
            $this->hashService->hash($email);

            $this->em->persist($email);
            $this->em->flush();
            $output->writeln($count . ' Email ' . $email->getEmail() . ' successfully encrypted!');

            $count++;
        }
    }
}
