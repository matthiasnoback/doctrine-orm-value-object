<?php

namespace Noback\DoctrineOrmValueObject\Mapping;

interface ValueObjectMappingDriverInterface
{
    public function getFieldMappings($class, $fieldPrefix);
}
