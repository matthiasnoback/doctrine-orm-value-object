<?php

namespace Noback\DoctrineOrmValueObject\Tests\Synchronizer\Fixtures;

class Entity
{
    private $valueObject;
    private $valueObject_property1;
    private $valueObject_property2;
    private $untouched = 'untouched';

    public function setValueObject(ValueObject $valueObject)
    {
        $this->valueObject = $valueObject;
    }

    public function getValueObject()
    {
        return $this->valueObject;
    }

    public function setValueObjectProperty1($valueObject_property1)
    {
        $this->valueObject_property1 = $valueObject_property1;
    }

    public function getValueObjectProperty1()
    {
        return $this->valueObject_property1;
    }

    public function setValueObjectProperty2($valueObject_property2)
    {
        $this->valueObject_property2 = $valueObject_property2;
    }

    public function getValueObjectProperty2()
    {
        return $this->valueObject_property2;
    }

    public function getUntouched()
    {
        return $this->untouched;
    }
}
