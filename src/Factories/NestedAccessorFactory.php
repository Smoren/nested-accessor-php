<?php

namespace Smoren\NestedAccessor\Factories;

use Smoren\NestedAccessor\Interfaces\NestedAccessorFactoryInterface;
use Smoren\NestedAccessor\Components\NestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

/**
 * Class NestedAccessorFactory
 * @author Smoren <ofigate@gmail.com>
 */
class NestedAccessorFactory implements NestedAccessorFactoryInterface
{
    /**
     * @inheritDoc
     * @throws NestedAccessorException
     */
    public static function create(&$source, string $pathDelimiter = '.'): NestedAccessor
    {
        return new NestedAccessor($source, $pathDelimiter);
    }
}
