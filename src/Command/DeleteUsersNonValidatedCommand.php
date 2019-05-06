<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\AdminUser;
use App\Entity\Email;
use App\Entity\User;
use App\Service\EmailManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DeleteUsersNonValidatedCommand extends Command
{
    const DAYS = 2;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:delete-users-novalidated';

    private $em;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Delete a new admin user.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to delete an admin user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Delete users non validated',
            '============',
        ]);


        $this->em->getRepository(Email::class)->deleteNonValidatedAfter(self::DAYS);
        $users = $this->em->getRepository(User::class)->deleteNonValidatedAfter(self::DAYS);

        $output->writeln('Ya');

    }
}