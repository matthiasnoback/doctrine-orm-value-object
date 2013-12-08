<?php

namespace Noback\DoctrineOrmValueObject\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class ValueObject extends Annotation
{
    public $class;
    public $fieldPrefix;
}
