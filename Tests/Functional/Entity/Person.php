<?php

namespace Noback\DoctrineOrmValueObject\Tests\Functional\Entity;

use Noback\DoctrineOrmValueObject\Annotation\ValueObject;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * @Entity
 */
class Person
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ValueObject(class="Noback\DoctrineOrmValueObject\Tests\Functional\Entity\PhoneNumber")
     */
    private $phoneNumber;

    private $phoneNumber_number;

    private $phoneNumber_since;

    public function getId()
    {
        return $this->id;
    }

    public function setPhoneNumber(PhoneNumber $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
}
