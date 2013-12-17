<?php

namespace Noback\DoctrineOrmValueObject\Tests\Synchronizer\Fixtures;

class ValueObject
{
    private $property1;
    private $property2;
    private $extraProperty; // a field that is not represented in the Entity

    public function setProperty1($property1)
    {
        $this->property1 = $property1;
    }

    public function getProperty1()
    {
        return $this->property1;
    }

    public function setProperty2($property2)
    {
        $this->property2 = $property2;
    }

    public function getProperty2()
    {
        return $this->property2;
    }
}
