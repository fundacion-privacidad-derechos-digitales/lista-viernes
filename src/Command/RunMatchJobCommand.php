<?php

namespace App\Command;

use App\Entity\CompareTemp;
use App\Entity\MatchJob;
use App\Service\EmailManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class RunMatchJobCommand extends Command
{
    protected static $defaultName = 'app:run-match-job';

    private $em;
    private $matchesDir;
    private $resultsDir;
    private $encoder;
    private $fileSystem;
    private $emailManager;

    public function __construct(ObjectManager $entityManager, ParameterBagInterface $params, EncoderInterface $encoder, Filesystem $filesystem, EmailManager $emailManager)
    {
        $this->em = $entityManager;
        $this->matchesDir = $params->get('matches_directory');
        $this->resultsDir = $params->get('results_directory');
        $this->encoder = $encoder;
        $this->fileSystem = $filesystem;
        $this->emailManager = $emailManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Run the next Match Job.')
            ->setHelp('This command allows you to run the next Match Job...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Seleccionar el siguiente MatchJob pending
        /** @var MatchJob $match */
        $match = $this->em->getRepository(MatchJob::class)->findFirstPending();

        if ($match === null) {
            return;
        }

        $output->writeln([
            'Run Match Job',
            '============',
        ]);

        // Primero borramos el contenido de la tabla
        $this->em->getRepository(CompareTemp::class)->deleteTable();

        // Cargar el fichero CSV en la tabla temporal
        $filename = $this->matchesDir . '/' . $match->getFilename();
        $this->em->getRepository(CompareTemp::class)->loadCSV($filename);

        // Ejecutar el match
        if ($match->getType() == MatchJob::TYPE_EMAIL) {
            $result = $this->em->getRepository(CompareTemp::class)->compareEmails();
        } else {
            $result = $this->em->getRepository(CompareTemp::class)->comparePhones();
        }

        $coincidences = count($result);

        $output->writeln('Coincidencias encontradas: ' . $coincidences);

        // Guardar el resultado en un fichero CSV
        $match->setResultFilename($match->generateUniqueFilename());
        $resultFileNamePath = $this->resultsDir . '/' . $match->getResultFileName();
        $this->fileSystem->dumpFile($resultFileNamePath, $this->encoder->encode($result, 'csv'));
        $match->setStatus(MatchJob::STATUS_DONE);
        $match->setCoincidences($coincidences);
        $this->em->persist($match);
        $this->em->flush();

        $this->emailManager->sendMatchJobFinishedEmail($match->getPartyUser()->getEmail());

        $output->writeln('<info>¡Hecho!</info>');
    }
}