<?php

namespace Noback\DoctrineOrmValueObject\Metadata;

use Metadata\MergeableClassMetadata;

class ClassMetadata extends MergeableClassMetadata
{
    public function addValueObject($propertyName, $valueObjectClass, $fieldPrefix)
    {
        $propertyMetadata = new PropertyMetadata($this->name, $propertyName, $valueObjectClass, $fieldPrefix);

        $this->addPropertyMetadata($propertyMetadata);
    }

    public function hasValueObjects()
    {
        return count($this->propertyMetadata) > 0;
    }
}
