<?php

namespace Noback\DoctrineOrmValueObject\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;
use Noback\DoctrineOrmValueObject\Annotation\ValueObject;
use Noback\DoctrineOrmValueObject\Metadata\ClassMetadata;

class AnnotationDriver implements DriverInterface
{
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function loadMetadataForClass(\ReflectionClass $valueObjectClass)
    {
        $metadata = new ClassMetadata($valueObjectClass->name);

        foreach ($valueObjectClass->getProperties() as $property) {
            $valueObjectAnnotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                'Noback\DoctrineOrmValueObject\Annotation\ValueObject'
            );

            if (!($valueObjectAnnotation instanceof ValueObject)) {
                continue;
            }

            $valueObjectClass = $valueObjectAnnotation->class;
            $fieldPrefix = $valueObjectAnnotation->fieldPrefix;

            $metadata->addValueObject($property->name, $valueObjectClass, $fieldPrefix);
        }

        return $metadata;
    }
}
