<?php

namespace Smoren\NestedAccessor\Interfaces;

/**
 * Interface NestedAccessorInterface
 * @author Smoren <ofigate@gmail.com>
 */
interface SilentNestedAccessorInterface
{
    /**
     * Getter of source value by nested path
     * @param string|array<string> $path nested path
     * @return mixed value got by path
     */
    public function get($path);

    /**
     * Setter for saving value to source by nested path
     * @param string|array<string> $path nested path
     * @param mixed $value value to save to source by path
     */
    public function set($path, $value): SilentNestedAccessorInterface;

    /**
     * Appender of source part specified by nested path
     * @param string|array<string> $path nested path
     * @param mixed $value value to save by path
     * @return $this
     */
    public function append($path, $value): SilentNestedAccessorInterface;

    /**
     * Deleter of source part specified by nested path
     * @param string|array<string> $path nested path
     * @return $this
     */
    public function delete($path): self;

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
