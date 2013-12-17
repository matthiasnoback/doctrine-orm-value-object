<?php

namespace Noback\DoctrineOrmValueObject\Tests\Functional\Entity;

use Doctrine\ORM\Mapping\Column;

class PhoneNumber
{
    /**
     * @Column(type="string", length=255)
     */
    private $number;

    /**
     * @Column(type="datetime")
     */
    private $since;

    public function __construct($number, \DateTime $since)
    {
        $this->number = $number;
        $this->since = $since;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getSince()
    {
        return $this->since;
    }
}
