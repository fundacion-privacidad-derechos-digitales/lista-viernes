<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeUserCommand extends Command
{
    protected static $defaultName = 'app:normalize:user';

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Normalize the User email and idNumber');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $count = 1;

        foreach ($users as $user) {
            $oldEmail = $user->getEmail();
            $oldIdNumber = $user->getIdNumber();

            $user->setEmail($oldEmail);
            $user->setIdNumber($oldIdNumber);

            if ($oldEmail !== $user->getEmail() || $oldIdNumber !== $user->getIdNumber()) {
                $this->em->persist($user);
                $this->em->flush();
                $output->writeln($count . ' User ' . $user->getEmail() . ' successfully normalized!');
            } else {
                $output->writeln($count . ' User ' . $user->getEmail() . ' normalization not necessary');
            }

            $count++;
        }
    }
}
