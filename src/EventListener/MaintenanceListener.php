<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener
{
    private $templating;
    private $maintenanceFileDir;
    private $filesystem;

    public function __construct(\Twig_Environment $templating, ParameterBagInterface $params, Filesystem $filesystem)
    {
        $this->templating = $templating;
        $this->maintenanceFileDir = $params->get('maintenance_file');
        $this->filesystem = $filesystem;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->filesystem->exists($this->maintenanceFileDir)) return;

        $event->setResponse(new Response($this->templating->render('maintenance.html.twig'), Response::HTTP_SERVICE_UNAVAILABLE));
        $event->stopPropagation();
    }
}