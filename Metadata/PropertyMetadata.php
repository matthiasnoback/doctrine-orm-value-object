<?php

namespace Noback\DoctrineOrmValueObject\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    public $valueObjectClass;
    public $fieldPrefix;

    public function __construct($class, $name, $valueObjectClass, $fieldPrefix)
    {
        parent::__construct($class, $name);

        if (!class_exists($valueObjectClass)) {
            throw new \InvalidArgumentException(sprintf(
                'Value object class "%s" does not exist',
                $valueObjectClass
            ));
        }

        $this->valueObjectClass = $valueObjectClass;

        if ($fieldPrefix === null) {
            $fieldPrefix = $name.'_';
        }

        $this->fieldPrefix = $fieldPrefix;
    }

    public function getValueObjectClass()
    {
        return $this->valueObjectClass;
    }

    public function getFieldPrefix()
    {
        return $this->fieldPrefix;
    }

    public function serialize()
    {
        return serialize(
            array(
                $this->class,
                $this->name,
                $this->valueObjectClass,
                $this->fieldPrefix
            )
        );
    }

    public function unserialize($str)
    {
        list($this->class, $this->name, $this->valueObjectClass, $this->fieldPrefix) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
