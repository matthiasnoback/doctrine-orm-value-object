<?php

namespace Noback\DoctrineOrmValueObject\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Metadata\MetadataFactory;

class StoreValueObjectsEventListener implements EventSubscriber
{
    private $metadataFactory;

    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::preFlush,
        );
    }

    public function preFlush(PreFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        /* @var $em \Doctrine\ORM\EntityManager */
        foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            $this->copyValuesFromValueObjects($entity);
        }

        foreach ($em->getUnitOfWork()->getIdentityMap() as $identities) {
            foreach ($identities as $entity) {
                $this->copyValuesFromValueObjects($entity);
            }
        }
    }

    private function copyValuesFromValueObjects($entity)
    {
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));
        /* @var $metadata \Noback\DoctrineOrmValueObject\Metadata\ClassMetadata */

        if (!$metadata->hasValueObjects()) {
            return;
        }

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /* @var $propertyMetadata \Noback\DoctrineOrmValueObject\Metadata\PropertyMetadata */

            $valueObject = $propertyMetadata->reflection->getValue($entity);

            $valueObjectClass = $propertyMetadata->getValueObjectClass();
            if (!($valueObject instanceof $valueObjectClass)) {
                continue;
            }

            $this->copyValueObjectPropertiesToEntityFields(
                $entity,
                $valueObject,
                $propertyMetadata->getFieldPrefix()
            );
        }
    }

    private function copyValueObjectPropertiesToEntityFields($entity, $valueObject, $fieldPrefix)
    {
        $entityReflectionClass = new \ReflectionClass(get_class($entity));
        $valueObjectReflectionClass = new \ReflectionClass(get_class($valueObject));

        foreach ($valueObjectReflectionClass->getProperties() as $valueObjectProperty) {
            $valueObjectProperty->setAccessible(true);
            $valueObjectValue = $valueObjectProperty->getValue($valueObject);
            $entityPropertyName = $fieldPrefix . $valueObjectProperty->name;

            $entityProperty = $entityReflectionClass->getProperty($entityPropertyName);
            $entityProperty->setAccessible(true);
            $entityProperty->setValue($entity, $valueObjectValue);
        }
    }
}
