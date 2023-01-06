<?php

namespace Smoren\NestedAccessor\Helpers;

use ArrayAccess;

class KeyAccessHelper
{
    /**
     * @template T
     * @param array<string, T>|ArrayAccess<string, T>|object|mixed $container
     * @param string $key
     * @param T|null $defaultValue
     * @return T|null
     */
    public static function get($container, string $key, $defaultValue = null)
    {
        switch(true) {
            case is_array($container):
                return static::getFromArray($container, $key, $defaultValue);
            case $container instanceof ArrayAccess:
                return static::getFromArrayAccess($container, $key, $defaultValue);
            case is_object($container):
                return static::getFromObject($container, $key, $defaultValue);
        }

        return $defaultValue;
    }

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed>|object|mixed $container
     * @param string $key
     * @return bool
     */
    public static function exists($container, string $key): bool
    {
        switch(true) {
            case is_array($container):
                return static::existsInArray($container, $key);
            case $container instanceof ArrayAccess:
                return static::existsInArrayAccess($container, $key);
            case is_object($container):
                return static::existsInObject($container, $key);
        }
        return false;
    }

    /**
     * @template T
     * @param array<string, T> $container
     * @param string $key
     * @param T|null $defaultValue
     * @return T|null
     */
    protected static function getFromArray(array $container, string $key, $defaultValue)
    {
        if(static::existsInArray($container, $key)) {
            return $container[$key];
        }

        return $defaultValue ?? null;
    }

    /**
     * @template T
     * @param array<string, T> $container
     * @param string $key
     * @return bool
     */
    protected static function existsInArray(array $container, string $key): bool
    {
        return array_key_exists($key, $container);
    }

    /**
     * @template T
     * @param ArrayAccess<string, T> $container
     * @param string $key
     * @param T|null $defaultValue
     * @return T|null
     */
    protected static function getFromArrayAccess(ArrayAccess $container, string $key, $defaultValue)
    {
        if(static::existsInArrayAccess($container, $key)) {
            return $container[$key];
        }

        return $defaultValue ?? null;
    }

    /**
     * @template T
     * @param ArrayAccess<string, T> $container
     * @param string $key
     * @return bool
     */
    protected static function existsInArrayAccess(ArrayAccess $container, string $key): bool
    {
        return $container->offsetExists($key);
    }

    /**
     * @param object $container
     * @param string $key
     * @param mixed|null $defaultValue
     * @return mixed|null
     */
    protected static function getFromObject(object $container, string $key, $defaultValue)
    {
        if(ObjectHelper::hasPublicProperty($container, $key)) {
            return $container->{$key} ?? $defaultValue;
        }

        if(ObjectHelper::hasPropertyAccessibleByGetter($container, $key)) {
            return ObjectHelper::getPropertyByGetter($container, $key);
        }

        return $defaultValue;
    }

    /**
     * @param object $container
     * @param string $key
     * @return bool
     */
    protected static function existsInObject(object $container, string $key): bool
    {
        return ObjectHelper::hasPublicProperty($container, $key)
            || ObjectHelper::hasPropertyAccessibleByGetter($container, $key);
    }
}
