<?php

namespace App\Command;

use App\Encrypt\EncryptionService;
use App\Entity\Phone;
use App\Hash\HashService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptPhonesCommand extends Command
{
    protected static $defaultName = 'app:encrypt:phones';

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
        $this->setDescription('Encrypt the Phone table');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phones = $this->em->getRepository(Phone::class)->findAll();

        $count = 1;

        foreach ($phones as $phone) {
            $phone->setPhone($phone->getPhone());
            $this->encryptionService->encrypt($phone);
            $this->hashService->hash($phone);

            $this->em->persist($phone);
            $this->em->flush();
            $output->writeln($count . ' Phone ' . $phone->getPhone() . ' successfully encrypted!');

            $count++;
        }
    }
}
