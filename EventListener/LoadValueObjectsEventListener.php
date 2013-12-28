<?php

namespace Noback\DoctrineOrmValueObject\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Noback\DoctrineOrmValueObject\Synchronizer\EntityToValueObjectSynchronizer;

class LoadValueObjectsEventListener implements EventSubscriber
{
    private $synchronizer;

    public function __construct(EntityToValueObjectSynchronizer $synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad,
        );
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        $this->synchronizer->synchronize($entity);
    }
}
