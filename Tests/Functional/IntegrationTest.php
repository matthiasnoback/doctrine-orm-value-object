<?php

namespace Noback\DoctrineOrmValueObject\Tests\Functional;

use Noback\DoctrineOrmValueObject\Test\DoctrineOrmValueObjectServiceProvider;
use Noback\DoctrineOrmValueObject\Tests\Functional\Entity\Person;
use Noback\DoctrineOrmValueObject\Tests\Functional\Entity\PhoneNumber;
use Noback\PHPUnitTestServiceContainer\PHPUnit\AbstractTestCaseWithEntityManager;

class IntegrationTest extends AbstractTestCaseWithEntityManager
{
    protected function getServiceProviders()
    {
        $serviceProviders = parent::getServiceProviders();

        $serviceProviders[] = new DoctrineOrmValueObjectServiceProvider();

        return $serviceProviders;
    }

    protected function getEntityDirectories()
    {
        return array(
            __DIR__.'/Entity'
        );
    }

    /**
     * @test
     */
    public function the_entity_manager_stores_and_retrieves_value_objects()
    {
        $number = '0614995363';
        $since = new \DateTime('yesterday');

        $person = new Person();
        $person->setPhoneNumber(new PhoneNumber($number, $since));

        $this->getEntityManager()->persist($person);
        $this->getEntityManager()->flush($person);

        // to make sure we don't get the exact same object back from the database
        $this->getEntityManager()->clear();

        $retrievedPerson = $this->getEntityManager()->find(get_class($person), $person->getId());
        if (!($retrievedPerson instanceof Person)) {
            $this->fail('We should be able to get a record back from the database');
        }

        $this->assertNotSame($person, $retrievedPerson);
        $this->assertEquals($number, $retrievedPerson->getPhoneNumber()->getNumber());
        $this->assertEquals($since, $retrievedPerson->getPhoneNumber()->getSince());
    }
}
