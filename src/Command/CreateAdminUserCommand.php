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

class CreateAdminUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-admin-user';

    private $em;
    private $passwordEncoder;
    private $emailManager;

    public function __construct(ObjectManager $entityManager, UserPasswordEncoderInterface $passwordEncoder, EmailManager $emailManager)
    {
        $this->em = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailManager = $emailManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new admin user.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create an admin user...')
            // configure an argument
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the user.')
            ->addArgument('lastname', InputArgument::REQUIRED, 'The last name of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Admin User Creator',
            '============',
        ]);

        $user = $this->em->getRepository(AdminUser::class)->findOneBy([
            'email' => $input->getArgument('email')
        ]);
        if (isset($user)) {
            $output->writeln('Admin user with email ' . $input->getArgument('email') . ' already exits.');
        } else {
            $user = new AdminUser();
            $user->setEmail($input->getArgument('email'))
                ->setName($input->getArgument('name'))
                ->setSurname($input->getArgument('lastname'));

            $pass = $user->generateRandomPassword();

            // encode the plain password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $pass));
            $this->em->persist($user);
            $this->em->flush();

            $this->emailManager->sendWelcomeAdminEmail($user, $pass);

            // retrieve the argument value using getArgument()
            $output->writeln('Admin user cerated with this email: '
                . $input->getArgument('email'));
        }
    }
}