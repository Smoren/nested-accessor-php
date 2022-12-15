<?php

namespace Smoren\NestedAccessor\Interfaces;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

/**
 * Interface NestedAccessorFactoryInterface
 * @author Smoren <ofigate@gmail.com>
 */
interface NestedAccessorFactoryInterface
{
    /**
     * Creates NestedAccessorInterface instance
     * @param array<int|string, mixed>|object|null $source source for accessing
     * @param non-empty-string $pathDelimiter nesting path separator
     * @throws NestedAccessorException
     */
    public static function create(&$source, string $pathDelimiter = '.'): NestedAccessorInterface;

    /**
     * Creates NestedAccessorInterface instance
     * @param array<mixed> $source source for accessing
     * @param non-empty-string $pathDelimiter nesting path separator
     */
    public static function fromArray(array &$source, string $pathDelimiter = '.'): NestedAccessorInterface;

    /**
     * Creates NestedAccessorInterface instance
     * @param object $source source for accessing
     * @param non-empty-string $pathDelimiter nesting path separator
     */
    public static function fromObject(object &$source, string $pathDelimiter = '.'): NestedAccessorInterface;
}
