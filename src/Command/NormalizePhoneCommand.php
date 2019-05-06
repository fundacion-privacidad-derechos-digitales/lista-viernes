<?php

namespace App\Command;

use App\Entity\Phone;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizePhoneCommand extends Command
{
    protected static $defaultName = 'app:normalize:phone';

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Normalize the Phone');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phones = $this->em->getRepository(Phone::class)->findAll();

        $count = 1;

        foreach ($phones as $phone) {
            $oldPhone = $phone->getPhone();

            $phone->setPhone($oldPhone);

            if ($oldPhone !== $phone->getPhone()) {
                $this->em->persist($phone);
                $this->em->flush();
                $output->writeln($count . ' Phone ' . $phone->getPhone() . ' successfully normalized!');
            } else {
                $output->writeln($count . ' User ' . $phone->getPhone() . ' normalization not necessary');
            }

            $count++;
        }
    }
}