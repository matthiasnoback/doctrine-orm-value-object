<?php

namespace Noback\DoctrineOrmValueObject\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Metadata\MetadataFactory;
use Noback\DoctrineOrmValueObject\Mapping\ValueObjectMappingDriverInterface;
use Noback\DoctrineOrmValueObject\Metadata\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineOrmClassMetadata;
use Noback\DoctrineOrmValueObject\Metadata\PropertyMetadata;

class AddValueObjectMappingEventSubscriber implements EventSubscriber
{
    private $metadataFactory;
    private $annotationReader;
    private $mappingDriver;

    public function __construct(
        MetadataFactory $metadataFactory,
        Reader $annotationReader,
        ValueObjectMappingDriverInterface $mappingDriver
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->annotationReader = $annotationReader;
        $this->mappingDriver = $mappingDriver;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata
        );
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $className = $event->getClassMetadata()->getName();
        $valueObjectMetadata = $this->metadataFactory->getMetadataForClass($className);
        /* @var $valueObjectMetadata ClassMetadata */

        if (!$valueObjectMetadata->hasValueObjects()) {
            return;
        }

        foreach ($valueObjectMetadata->propertyMetadata as $valueObjectPropertyMetadata) {
            $this->addValueObjectFields($event->getClassMetadata(), $valueObjectPropertyMetadata);
        }
    }

    /**
     * @param LoadClassMetadataEventArgs $event
     * @param $valueObjectPropertyMetadata
     */
    private function addValueObjectFields(
        DoctrineOrmClassMetadata $classMetadata,
        PropertyMetadata $valueObjectPropertyMetadata
    ) {
        $valueObjectClass = $valueObjectPropertyMetadata->getValueObjectClass();
        $fieldPrefix = $valueObjectPropertyMetadata->getFieldPrefix();

        $extraFields = $this->getValueObjectFieldMappings($valueObjectClass, $fieldPrefix);

        foreach ($extraFields as $fieldMapping) {
            $classMetadata->mapField($fieldMapping);
        }
    }

    private function getValueObjectFieldMappings($class, $fieldPrefix)
    {
        return $this->mappingDriver->getFieldMappings($class, $fieldPrefix);
    }
}
