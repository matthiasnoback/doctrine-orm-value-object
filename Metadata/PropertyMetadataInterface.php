<?php

namespace Noback\DoctrineOrmValueObject\Metadata;

interface PropertyMetadataInterface
{
    public function getValueObjectClass();

    public function getFieldPrefix();

    public function getValue($object);
}
