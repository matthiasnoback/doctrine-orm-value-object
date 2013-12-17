<?php

namespace Noback\DoctrineOrmValueObject\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Noback\DoctrineOrmValueObject\Synchronizer\ValueObjectToEntitySynchronizer;

class StoreValueObjectsEventListener implements EventSubscriber
{
    private $synchronizer;

    public function getSubscribedEvents()
    {
        return array(
            Events::preFlush
        );
    }

    public function __construct(ValueObjectToEntitySynchronizer $synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    public function preFlush(PreFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        /* @var $em \Doctrine\ORM\EntityManager */
        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            $this->synchronizeValueObjects($entity);
        }

        foreach ($em->getUnitOfWork()->getIdentityMap() as $identities) {
            foreach ($identities as $entity) {
                $this->synchronizeValueObjects($entity);
            }
        }
    }

    private function synchronizeValueObjects($entity)
    {
        $this->synchronizer->synchronize($entity);
    }
}
