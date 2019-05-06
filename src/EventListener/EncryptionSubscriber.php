<?php

namespace App\EventListener;

use App\Encrypt\EncryptInterface;
use App\Encrypt\EncryptionService;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class EncryptionSubscriber implements EventSubscriber
{
    private $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postLoad
        ];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof EncryptInterface) {
            $this->encryptionService->decrypt($entity);
        }
    }
}
