<?php

namespace Smoren\NestedAccessor\Interfaces;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

/**
 * Interface NestedAccessorInterface
 * @author Smoren <ofigate@gmail.com>
 */
interface NestedAccessorInterface
{
    /**
     * Getter of source value by nested path
     * @param string|array<string> $path nested path
     * @param bool $strict when true throw exception if path not exist in source data
     * @return mixed value got by path
     * @throws NestedAccessorException
     */
    public function get($path, bool $strict = true);

    /**
     * Setter for saving value to source by nested path
     * @param string|array<string> $path nested path
     * @param mixed $value value to save to source by path
     * @param bool $strict when true throw exception if path not exist in source object
     * @return NestedAccessorInterface this
     * @throws NestedAccessorException
     */
    public function set($path, $value, bool $strict = true): NestedAccessorInterface;

    /**
     * Appender of source part specified by nested path
     * @param string|array<string> $path nested path
     * @param mixed $value value to save by path
     * @param bool $strict when true throw exception if path not exist in source object
     * @return $this
     * @throws NestedAccessorException
     */
    public function append($path, $value, bool $strict = true): NestedAccessorInterface;

    /**
     * Returns true if path exists, false otherwise
     * @param string|string[] $path nested path
     * @return bool
     */
    public function exist($path): bool;

    /**
     * Returns true if path exists and not null, false otherwise
     * @param string|string[] $path nested path
     * @return bool
     */
    public function isset($path): bool;
}
