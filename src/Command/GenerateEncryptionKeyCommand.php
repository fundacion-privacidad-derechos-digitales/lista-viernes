<?php

namespace App\Command;

use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateEncryptionKeyCommand extends Command
{
    protected static $defaultName = 'app:encrypt:generate-key';

    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Generate a new encryption key');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keyFilePath = $this->kernel->getProjectDir() . '/.key';
        KeyFactory::save(KeyFactory::generateEncryptionKey(), $keyFilePath);

        $output->writeln('Key generated successfully in ' . $keyFilePath);
    }
}
