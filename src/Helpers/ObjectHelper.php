<?php

namespace Smoren\NestedAccessor\Helpers;

use ReflectionMethod;
use ReflectionProperty;
use stdClass;

/**
 * Helper for accessing object properties and methods
 */
class ObjectHelper
{
    /**
     * @param object $object
     * @param string $propertyName
     * @return bool
     */
    public static function hasPublicProperty(object $object, string $propertyName): bool
    {
        if ($object instanceof stdClass) {
            return static::hasProperty($object, $propertyName);
        }

        return
            static::hasProperty($object, $propertyName) &&
            static::getReflectionProperty($object, $propertyName)->isPublic();
    }

    /**
     * @param object $object
     * @param string $methodName
     * @return bool
     */
    public static function hasPublicMethod(object $object, string $methodName): bool
    {
        return
            static::hasMethod($object, $methodName) &&
            static::getReflectionMethod($object, $methodName)->isPublic();
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return bool
     */
    public static function hasPropertyAccessibleByGetter(object $object, string $propertyName): bool
    {
        return static::hasPublicMethod($object, static::getPropertyGetterName($propertyName));
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    public static function getPropertyByGetter(object $object, string $propertyName)
    {
        return $object->{static::getPropertyGetterName($propertyName)}();
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return bool
     */
    public static function hasProperty(object $object, string $propertyName): bool
    {
        return property_exists($object, $propertyName);
    }

    /**
     * @param object $object
     * @param string $methodName
     * @return bool
     */
    public static function hasMethod(object $object, string $methodName): bool
    {
        return method_exists($object, $methodName);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return ReflectionProperty
     */
    protected static function getReflectionProperty(object $object, string $propertyName): ReflectionProperty
    {
        return new ReflectionProperty(get_class($object), $propertyName);
    }

    /**
     * @param object $object
     * @param string $methodName
     * @return ReflectionMethod
     */
    protected static function getReflectionMethod(object $object, string $methodName): ReflectionMethod
    {
        return new ReflectionMethod(get_class($object), $methodName);
    }

    /**
     * @param string $propertyName
     * @return string
     */
    protected static function getPropertyGetterName(string $propertyName): string
    {
        return 'get'.ucfirst($propertyName);
    }
}
