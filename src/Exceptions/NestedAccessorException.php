<?php

namespace Smoren\NestedAccessor\Exceptions;

use Smoren\ExtendedExceptions\BaseException;
use Smoren\NestedAccessor\Components\NestedAccessor;

/**
 * Class NestedAccessorException
 * @author Smoren <ofigate@gmail.com>
 */
class NestedAccessorException extends BaseException
{
    public const SOURCE_IS_SCALAR = 1;
    public const CANNOT_GET_VALUE = 2;
    public const CANNOT_SET_VALUE = 3;
    public const CANNOT_APPEND_VALUE = 4;
    public const CANNOT_DELETE_VALUE = 5;
    public const UNKNOWN_SET_MODE = 6;

    /**
     * Creates a new exception instance for "source is scalar" error
     * @param mixed $source source
     * @return NestedAccessorException
     */
    public static function createAsSourceIsScalar($source): NestedAccessorException
    {
        return new NestedAccessorException(
            'source is scalar',
            NestedAccessorException::SOURCE_IS_SCALAR,
            null,
            [
                'source_type' => gettype($source),
            ]
        );
    }

    /**
     * Creates a new exception instance for "cannot get value" error
     * @param string $path path key
     * @param int $count errors count
     * @return NestedAccessorException
     */
    public static function createAsCannotGetValue(string $path, int $count): NestedAccessorException
    {
        return new NestedAccessorException(
            "cannot get value by path '{$path}'",
            NestedAccessorException::CANNOT_GET_VALUE,
            null,
            [
                'path' => $path,
                'count' => $count,
            ]
        );
    }

    /**
     * Creates a new exception instance for "cannot set|append|delete value" error
     * @param int $mode set mode
     * @param string $path path key
     * @return NestedAccessorException
     */
    public static function createAsCannotSetValue(int $mode, string $path): NestedAccessorException
    {
        switch($mode) {
            case NestedAccessor::SET_MODE_SET:
                return static::_createAsCannotSetValue($path);
            case NestedAccessor::SET_MODE_APPEND:
                return static::_createAsCannotAppendValue($path);
            case NestedAccessor::SET_MODE_DELETE:
                return static::_createAsCannotDeleteValue($path);
            default:
                return static::_createAsUnknownSetMode($mode);
        }
    }

    /**
     * Creates a new exception instance for "cannot set value" error
     * @param string $path path key
     * @return NestedAccessorException
     */
    protected static function _createAsCannotSetValue(string $path): NestedAccessorException
    {
        return new NestedAccessorException(
            "cannot set value by path '{$path}'",
            NestedAccessorException::CANNOT_SET_VALUE,
            null,
            [
                'path' => $path,
            ]
        );
    }

    /**
     * Creates a new exception instance for "cannot append value" error
     * @param string $path path key
     * @return NestedAccessorException
     */
    protected static function _createAsCannotAppendValue(string $path): NestedAccessorException
    {
        return new NestedAccessorException(
            "cannot append value by path '{$path}'",
            NestedAccessorException::CANNOT_APPEND_VALUE,
            null,
            [
                'path' => $path,
            ]
        );
    }

    /**
     * Creates a new exception instance for "cannot delete value" error
     * @param string $path path key
     * @return NestedAccessorException
     */
    protected static function _createAsCannotDeleteValue(string $path): NestedAccessorException
    {
        return new NestedAccessorException(
            "cannot delete value by path '{$path}'",
            NestedAccessorException::CANNOT_DELETE_VALUE,
            null,
            [
                'path' => $path,
            ]
        );
    }

    /**
     * Creates a new exception instance for "unknown set mode" error
     * @param int $mode set mode
     * @return NestedAccessorException
     */
    protected static function _createAsUnknownSetMode(int $mode): NestedAccessorException
    {
        return new NestedAccessorException(
            "unknown set mode",
            NestedAccessorException::UNKNOWN_SET_MODE,
            null,
            [
                'mode' => $mode,
            ]
        );
    }
}
