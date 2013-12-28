<?php

namespace Noback\DoctrineOrmValueObject\Synchronizer;

use Metadata\MetadataFactoryInterface;
use Noback\DoctrineOrmValueObject\Metadata\PropertyMetadata;

class EntityToValueObjectSynchronizer
{
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function synchronize($entity)
    {
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));

        $entityReflectionClass = new \ReflectionClass(get_class($entity));

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            $this->synchronizeProperty($entity, $propertyMetadata, $entityReflectionClass);
        }
    }

    private function synchronizeProperty(
        $entity,
        PropertyMetadata $propertyMetadata,
        \ReflectionClass $entityReflectionClass
    ) {
        $valueObjectClass = $propertyMetadata->getValueObjectClass();
        $valueObjectClassReflection = new \ReflectionClass($valueObjectClass);

        $valueObject = $this->createValueObject($valueObjectClass);

        foreach ($valueObjectClassReflection->getProperties() as $valueObjectProperty) {
            $valueObjectProperty->setAccessible(true);
            $entityPropertyName = $propertyMetadata->getFieldPrefix() . $valueObjectProperty->getName();

            $entityProperty = $entityReflectionClass->getProperty($entityPropertyName);
            $entityProperty->setAccessible(true);
            $entityPropertyValue = $entityProperty->getValue($entity);

            $valueObjectProperty->setValue($valueObject, $entityPropertyValue);
        }

        $propertyMetadata->setValue($entity, $valueObject);
    }

    private function createValueObject($class)
    {
        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class));
    }
}
