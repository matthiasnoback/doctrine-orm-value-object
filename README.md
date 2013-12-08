# Value objects for Doctrine ORM

**WARNING: this library is not ready for production yet**

This library provides the tools for working with value objects and Doctrine entities.

This is the way you could persist a ``PhoneNumber`` value object as a field of a ``Person`` entity using the ``ValueObject`` annotation:

```php
<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Acme\DemoBundle\Value\PhoneNumber;
use Noback\DoctrineOrmValueObject\Annotation\ValueObject;

/**
 * @ORM\Entity
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ValueObject(class="Acme\DemoBundle\Value\PhoneNumber")
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

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
}
```

The ``Person`` entity should have a corresponding property for each of the properties of the ``PhoneNumber`` class.

```php
<?php

namespace Acme\DemoBundle\Value;

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
```

The ``PhoneNumber`` class contains the column definitions for the corresponding fields of the ``Person`` entity.

Event listeners make sure that:

- When a ``Person`` entity is being persisted or updated, the fields of the entity will contain the values from the ``PhoneNumber`` value object.
- When a ``Person`` entity is loaded from the database, the ``phoneNumber`` field will contain a ``PhoneNumber`` object. Its values will be copied from the designated entity fields.
- When the database schema is created or updated, the fields required for storing the ``PhoneNumber`` value object will be added to the ``Person`` entity's schema.

## Current limitations

- Only Doctrine column annotations are supported
