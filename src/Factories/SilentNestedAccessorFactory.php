<?php

namespace Smoren\NestedAccessor\Factories;

use Smoren\NestedAccessor\Components\SilentNestedAccessor;
use Smoren\NestedAccessor\Interfaces\SilentNestedAccessorFactoryInterface;
use Smoren\NestedAccessor\Interfaces\SilentNestedAccessorInterface;

/**
 * Class NestedAccessorFactory
 * @author Smoren <ofigate@gmail.com>
 */
class SilentNestedAccessorFactory implements SilentNestedAccessorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create(&$source, string $pathDelimiter = '.'): SilentNestedAccessorInterface
    {
        return new SilentNestedAccessor($source, $pathDelimiter);
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array &$source, string $pathDelimiter = '.'): SilentNestedAccessorInterface
    {
        return static::create($source, $pathDelimiter);
    }

    /**
     * @inheritDoc
     */
    public static function fromObject(object &$source, string $pathDelimiter = '.'): SilentNestedAccessorInterface
    {
        return static::create($source, $pathDelimiter);
    }
}
