<?php

namespace Smoren\NestedAccessor\Helpers;

use ReflectionMethod;
use ReflectionProperty;
use stdClass;

class ObjectHelper
{
    public static function hasPublicProperty(object $object, string $propertyName): bool
    {
        if ($object instanceof stdClass) {
            return static::hasProperty($object, $propertyName);
        }

        return
            static::hasProperty($object, $propertyName) &&
            static::getReflectionProperty($object, $propertyName)->isPublic();
    }

    public static function hasPublicMethod(object $object, string $methodName): bool
    {
        return
            static::hasMethod($object, $methodName) &&
            static::getReflectionMethod($object, $methodName)->isPublic();
    }

    public static function hasPropertyAccessibleByGetter(object $object, string $propertyName): bool
    {
        return static::hasPublicMethod($object, static::getPropertyGetterName($propertyName));
    }

    public static function getPropertyByGetter(object $object, string $propertyName)
    {
        return $object->{static::getPropertyGetterName($propertyName)}();
    }

    public static function hasProperty(object $object, string $propertyName): bool
    {
        return property_exists($object, $propertyName);
    }

    public static function hasMethod(object $object, string $methodName): bool
    {
        return method_exists($object, $methodName);
    }

    protected static function getReflectionProperty(object $object, string $propertyName): ReflectionProperty
    {
        return new ReflectionProperty(get_class($object), $propertyName);
    }

    protected static function getReflectionMethod(object $object, string $methodName): ReflectionMethod
    {
        return new ReflectionMethod(get_class($object), $methodName);
    }

    protected static function getPropertyGetterName(string $propertyName): string
    {
        return 'get'.ucfirst($propertyName);
    }
}
