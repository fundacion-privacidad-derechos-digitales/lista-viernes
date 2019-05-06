<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class MaintenanceCommand extends Command
{
    protected static $defaultName = 'app:maintenance';

    private $maintenanceFileDir;
    private $filesystem;

    public function __construct(ParameterBagInterface $params, Filesystem $filesystem)
    {
        $this->maintenanceFileDir = $params->get('maintenance_file');
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add the account email to the emails table if not exists')
            ->addArgument('mode', InputArgument::REQUIRED, 'Set mode "on" or "off"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = strtolower($input->getArgument('mode'));

        if (!in_array($mode, ['on', 'off'])) {
            $output->writeln([
                '<fg=red>Parámetro inválido. Ejemplos:</>',
                '<info>php bin/console app:maintenance on</info>',
                '<info>php bin/console app:maintenance off</info>'
            ]);
        }

        if ($mode === 'on' && !$this->filesystem->exists($this->maintenanceFileDir)) {
            $this->filesystem->touch($this->maintenanceFileDir);
            $output->writeln('<info>Modo mantenimiento activado</info>');
            return;
        }

        if ($mode === 'off' && $this->filesystem->exists($this->maintenanceFileDir)) {
            $this->filesystem->remove($this->maintenanceFileDir);
            $output->writeln('<info>Modo mantenimiento desactivado</info>');
            return;
        }
    }
}
