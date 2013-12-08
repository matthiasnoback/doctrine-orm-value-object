<?php

namespace Noback\DoctrineOrmValueObject\Mapping;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\Column;

class AnnotationDriver implements ValueObjectMappingDriverInterface
{
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function getFieldMappings($class, $fieldPrefix)
    {
        $fieldMappings = array();

        $reflectionClass = new \ReflectionClass($class);
        foreach ($reflectionClass->getProperties() as $property) {
            $columnAnnotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                'Doctrine\ORM\Mapping\Column'
            );

            if (!($columnAnnotation instanceof Column)) {
                continue;
            }

            $fieldMappings[] = $this->columnToArray($fieldPrefix . $property->getName(), $columnAnnotation);
        }

        return $fieldMappings;
    }

    private function columnToArray($fieldName, Column $column)
    {
        $mapping = array(
            'fieldName' => $fieldName,
            'type' => $column->type,
            'scale' => $column->scale,
            'length' => $column->length,
            'unique' => $column->unique,
            'nullable' => $column->nullable,
            'precision' => $column->precision
        );

        if ($column->options) {
            $mapping['options'] = $column->options;
        }

        if (isset($column->name)) {
            $mapping['columnName'] = $column->name;
        }

        if (isset($column->columnDefinition)) {
            $mapping['columnDefinition'] = $column->columnDefinition;
        }

        return $mapping;
    }
}
