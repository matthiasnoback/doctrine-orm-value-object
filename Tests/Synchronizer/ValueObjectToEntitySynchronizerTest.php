<?php

namespace Noback\DoctrineOrmValueObject\Tests\Synchronizer;

use Noback\DoctrineOrmValueObject\Synchronizer\ValueObjectToEntitySynchronizer;
use Noback\DoctrineOrmValueObject\Tests\Synchronizer\Fixtures\Entity;
use Noback\DoctrineOrmValueObject\Tests\Synchronizer\Fixtures\ValueObject;

class ValueObjectToEntitySynchronizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_synchronizes_a_value_object_with_an_entity_by_prefixing_the_fields()
    {
        $valueObject = new ValueObject();
        $valueObject->setProperty1('value1');
        $valueObject->setProperty2('value2');
        $entity = new Entity();
        $entity->setValueObject($valueObject);

        $properties = array(
            $this->createMockPropertyMetadata($valueObject, get_class($valueObject), 'valueObject_'),
        );
        $classMetadata = $this->createMockClassMetadata($properties);

        $metadataFactory = $this->createMockMetadataFactory(get_class($entity), $classMetadata);

        $synchronizer = new ValueObjectToEntitySynchronizer($metadataFactory);

        $synchronizer->synchronize($entity);

        $this->assertSame($entity->getValueObjectProperty1(), $valueObject->getProperty1());
        $this->assertSame($entity->getValueObjectProperty2(), $valueObject->getProperty2());

        // make sure the hydrator hasn't changed other fields too
        $this->assertSame('untouched', $entity->getUntouched());
    }

    private function createMockMetadataFactory($class, $classMetadata)
    {
        $metadataFactory = $this->getMockBuilder('Metadata\MetadataFactoryInterface')->disableOriginalConstructor()->getMock();

        $metadataFactory
            ->expects($this->any())
            ->method('getMetadataForClass')
            ->with($class)
            ->will($this->returnValue($classMetadata));

        return $metadataFactory;
    }

    private function createMockClassMetadata($properties)
    {
        $classMetadata = $this->getMock('Noback\DoctrineOrmValueObject\Metadata\ClassMetadataInterface');

        $classMetadata
            ->expects($this->any())
            ->method('getValueObjectProperties')
            ->will($this->returnValue($properties));

        return $classMetadata;
    }

    private function createMockPropertyMetadata($value, $valueObjectClass, $fieldPrefix)
    {
        $propertyMetadata = $this->getMock('Noback\DoctrineOrmValueObject\Metadata\PropertyMetadataInterface');

        $propertyMetadata
            ->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($value));
        $propertyMetadata
            ->expects($this->any())
            ->method('getValueObjectClass')
            ->will($this->returnValue($valueObjectClass));
        $propertyMetadata
            ->expects($this->any())
            ->method('getFieldPrefix')
            ->will($this->returnValue($fieldPrefix));

        return $propertyMetadata;
    }
}
