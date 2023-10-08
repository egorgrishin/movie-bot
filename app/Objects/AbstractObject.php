<?php

namespace App\Objects;

abstract class AbstractObject
{
    public static function fromObject(object $object): self
    {
        $class = new static();
        foreach (get_class_vars($class::class) as $property => $default) {
            $class->$property = $object->$property ?? $default;
        }
        return $class;
    }
}
