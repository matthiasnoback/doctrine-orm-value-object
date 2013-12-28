<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';

// Somehow annotations could not be auto-loaded correctly because of the package's "target-dir".
// I found this "hack" here: https://github.com/composer/composer/issues/925
AnnotationRegistry::registerLoader('class_exists');
