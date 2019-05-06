<?php

namespace App\Command;

use App\Encrypt\EncryptionService;
use App\Entity\User;
use App\Hash\HashService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptUsersCommand extends Command
{
    protected static $defaultName = 'app:encrypt:users';

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
        $this->setDescription('Encrypt the User table');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $count = 1;

        foreach ($users as $user) {
            $user->setEmail($user->getEmail());
            $user->setIdNumber($user->getIdNumber());

            $this->encryptionService->encrypt($user);
            $this->hashService->hash($user);

            $this->em->persist($user);
            $this->em->flush();
            $output->writeln($count . ' User ' . $user->getEmail() . ' successfully encrypted!');

            $count++;
        }
    }
}
