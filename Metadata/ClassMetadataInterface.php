<?php

namespace Noback\DoctrineOrmValueObject\Metadata;

interface ClassMetadataInterface
{
    public function addValueObject($propertyName, $valueObjectClass, $fieldPrefix);

    public function hasValueObjects();

    /**
     * @return PropertyMetadata[]
     */
    public function getValueObjectProperties();
}
