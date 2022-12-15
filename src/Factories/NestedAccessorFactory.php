<?php

namespace Smoren\NestedAccessor\Factories;

use Smoren\NestedAccessor\Interfaces\NestedAccessorFactoryInterface;
use Smoren\NestedAccessor\Components\NestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Interfaces\NestedAccessorInterface;

/**
 * Class NestedAccessorFactory
 * @author Smoren <ofigate@gmail.com>
 */
class NestedAccessorFactory implements NestedAccessorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create(&$source, string $pathDelimiter = '.'): NestedAccessor
    {
        return new NestedAccessor($source, $pathDelimiter);
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array &$source, string $pathDelimiter = '.'): NestedAccessorInterface
    {
        return static::create($source, $pathDelimiter);
    }

    /**
     * @inheritDoc
     */
    public static function fromObject(object &$source, string $pathDelimiter = '.'): NestedAccessorInterface
    {
        return static::create($source, $pathDelimiter);
    }
}
