<?php

namespace Noback\DoctrineOrmValueObject\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Metadata\MetadataFactory;

class LoadValueObjectsEventListener implements EventSubscriber
{
    private $metadataFactory;

    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
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

        $this->createValueObjects($entity);
    }

    private function createValueObjects($entity)
    {
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));

        $entityReflectionClass = new \ReflectionClass(get_class($entity));

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /* @var $propertyMetadata \Noback\DoctrineOrmValueObject\Metadata\PropertyMetadata */

            $valueObjectClass = $propertyMetadata->getValueObjectClass();
            $valueObjectClassReflection = new \ReflectionClass($valueObjectClass);

            $valueObject = $this->createValueObject($valueObjectClass);

            foreach ($valueObjectClassReflection->getProperties() as $valueObjectProperty) {
                $valueObjectProperty->setAccessible(true);
                $entityPropertyName = $propertyMetadata->getFieldPrefix().$valueObjectProperty->getName();

                $entityProperty = $entityReflectionClass->getProperty($entityPropertyName);
                $entityProperty->setAccessible(true);
                $entityPropertyValue = $entityProperty->getValue($entity);

                $valueObjectProperty->setValue($valueObject, $entityPropertyValue);
            }

            $propertyMetadata->setValue($entity, $valueObject);
        }
    }

    private function createValueObject($class)
    {
        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class));
    }
}
