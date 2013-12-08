<?php

namespace Noback\DoctrineOrmValueObject\Metadata;

use Metadata\ClassHierarchyMetadata as BaseClassHierarchyMetadata;

class ClassHierarchyMetadata extends BaseClassHierarchyMetadata
{
    public function hasValueObjects()
    {
        foreach ($this->classMetadata as $classMetadata) {
            /* @var $classMetadata \Noback\DoctrineOrmValueObject\Metadata\ClassMetadata */
            if ($classMetadata->hasValueObjects()) {
                return true;
            }
        }

        return false;
    }
}
