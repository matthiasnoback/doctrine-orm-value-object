<?php

namespace Noback\DoctrineOrmValueObject\Synchronizer;

use Metadata\MetadataFactoryInterface;

class ValueObjectToEntitySynchronizer
{
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function synchronize($entity)
    {
        $metadata = $this->getMetadataFor($entity);

        foreach ($metadata->getValueObjectProperties() as $propertyMetadata) {
            $valueObject = $propertyMetadata->getValue($entity);

            $valueObjectClass = $propertyMetadata->getValueObjectClass();
            if (!($valueObject instanceof $valueObjectClass)) {
                continue;
            }

            $this->synchronizeValueObjectPropertiesToEntityFields(
                $valueObject,
                $entity,
                $propertyMetadata->getFieldPrefix()
            );
        }
    }

    private function synchronizeValueObjectPropertiesToEntityFields($valueObject, $entity, $fieldPrefix)
    {
        $valueObjectValues = $this->collectValueObjectValues($valueObject);

        $this->hydrateEntity($entity, $valueObjectValues, $fieldPrefix);
    }

    private function collectValueObjectValues($valueObject)
    {
        $reflectionClass = new \ReflectionClass(get_class($valueObject));

        $values = array();

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $valueObjectValue = $property->getValue($valueObject);
            $values[$property->name] = $valueObjectValue;
        }

        return $values;
    }

    private function hydrateEntity($entity, $valueObjectValues, $fieldPrefix)
    {
        $prefixedValues = $this->getValuesWithPrefixedFields($valueObjectValues, $fieldPrefix);

        $reflectionClass = new \ReflectionClass(get_class($entity));

        foreach ($prefixedValues as $field => $value) {
            $propertyName = $field;

            if (!$reflectionClass->hasProperty($propertyName)) {
                continue;
            }

            $property = $reflectionClass->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($entity, $value);
        }
    }

    private function getValuesWithPrefixedFields(array $valueObjectValues, $fieldPrefix)
    {
        $fields = array_keys($valueObjectValues);

        $fieldsWithPrefix = array_map(
            function ($field) use ($fieldPrefix) {
                return $fieldPrefix . $field;
            },
            $fields
        );

        $valueObjectValues = array_combine($fieldsWithPrefix, $valueObjectValues);

        return $valueObjectValues;
    }

    /**
     * @return \Noback\DoctrineOrmValueObject\Metadata\ClassMetadata
     */
    private function getMetadataFor($entity)
    {
        return $this->metadataFactory->getMetadataForClass(get_class($entity));
    }
}
