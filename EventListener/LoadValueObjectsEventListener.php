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

            $valueObjectClassReflection = new \ReflectionClass($propertyMetadata->getValueObjectClass());
            $valueObject = $valueObjectClassReflection->newInstanceWithoutConstructor();

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
}
