<?php

namespace Noback\DoctrineOrmValueObject\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Metadata\MetadataFactory;
use Noback\DoctrineOrmValueObject\EventListener\AddValueObjectMappingEventSubscriber;
use Noback\DoctrineOrmValueObject\EventListener\LoadValueObjectsEventListener;
use Noback\DoctrineOrmValueObject\EventListener\StoreValueObjectsEventListener;
use Noback\DoctrineOrmValueObject\Metadata\Driver\AnnotationDriver as MetadataAnnotationDriver;
use Noback\DoctrineOrmValueObject\Synchronizer\EntityToValueObjectSynchronizer;
use Noback\DoctrineOrmValueObject\Synchronizer\ValueObjectToEntitySynchronizer;
use Noback\PHPUnitTestServiceContainer\ServiceContainerInterface;
use Noback\PHPUnitTestServiceContainer\ServiceProviderInterface;
use Noback\DoctrineOrmValueObject\Mapping\AnnotationDriver as MappingAnnotationDriver;

class DoctrineOrmValueObjectServiceProvider implements ServiceProviderInterface
{
    public function register(ServiceContainerInterface $container)
    {
        $container['doctrine_orm_value_object.annotation_reader'] = $container->share(
            function () {
                return new AnnotationReader();
            }
        );

        $container['doctrine_orm_value_object.annotation_driver'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new MetadataAnnotationDriver($container['doctrine_orm_value_object.annotation_reader']);
            }
        );

        $container['doctrine_orm_value_object.metadata_factory'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new MetadataFactory($container['doctrine_orm_value_object.annotation_driver']);
            }
        );

        $container['doctrine_orm_value_object.load_value_objects_listener'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new LoadValueObjectsEventListener($container['doctrine_orm_value_object.entity_to_value_object_synchronizer']);
            }
        );

        $container['doctrine_orm_value_object.entity_to_value_object_synchronizer'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new EntityToValueObjectSynchronizer($container['doctrine_orm_value_object.metadata_factory']);
            }
        );

        $container['doctrine_orm_value_object.value_object_to_entity_synchronizer'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new ValueObjectToEntitySynchronizer($container['doctrine_orm_value_object.metadata_factory']);
            }
        );

        $container['doctrine_orm_value_object.store_value_objects_listener'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new StoreValueObjectsEventListener($container['doctrine_orm_value_object.value_object_to_entity_synchronizer']);
            }
        );

        $container['doctrine_orm_value_object.mapping_driver'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new MappingAnnotationDriver($container['doctrine_orm_value_object.annotation_reader']);
            }
        );

        $container['doctrine_orm_value_object.add_value_object_mapping_listener'] = $container->share(
            function (ServiceContainerInterface $container) {
                return new AddValueObjectMappingEventSubscriber(
                    $container['doctrine_orm_value_object.metadata_factory'],
                    $container['doctrine_orm_value_object.annotation_reader'],
                    $container['doctrine_orm_value_object.mapping_driver']
                );
            }
        );

        $container['doctrine_dbal.event_manager'] = $container->extend(
            'doctrine_dbal.event_manager',
            function (EventManager $eventManager, ServiceContainerInterface $container) {
                $eventManager->addEventSubscriber(
                    $container['doctrine_orm_value_object.load_value_objects_listener']
                );
                $eventManager->addEventSubscriber(
                    $container['doctrine_orm_value_object.store_value_objects_listener']
                );
                $eventManager->addEventSubscriber(
                    $container['doctrine_orm_value_object.add_value_object_mapping_listener']
                );

                return $eventManager;
            }
        );
    }

    public function setUp(ServiceContainerInterface $container)
    {
    }

    public function tearDown(ServiceContainerInterface $container)
    {
    }
}
