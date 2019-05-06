<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\AdminUser;
use App\Service\EmailManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DeleteAdminUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:delete-admin-user';

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
            ->setHelp('This command allows you to delete an admin user...')
            // configure an argument
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Admin User Delete',
            '============',
        ]);


        $user = $this->em->getRepository(AdminUser::class)->findOneBy([
            'email' => $input->getArgument('email')
        ]);

        if(isset($user)){
            $this->em->remove($user);
            $this->em->flush();
            $output->writeln('Admin user deleted: '
                . $input->getArgument('email'));
        } else {
            $output->writeln('Admin user with email ' . $input->getArgument('email') . ' not found.');
        }
    }
}