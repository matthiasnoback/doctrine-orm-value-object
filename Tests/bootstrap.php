<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Noback\DoctrineOrmValueObject\Annotation\ValueObject;

$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$valueObject = new ValueObject(array());
