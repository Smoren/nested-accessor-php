<?php

namespace Smoren\NestedAccessor\Interfaces;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

/**
 * Interface NestedAccessorInterface
 * @author Smoren <ofigate@gmail.com>
 */
interface NestedAccessorInterface extends SilentNestedAccessorInterface
{
    /**
     * {@inheritDoc}
     * @throws NestedAccessorException
     */
    public function get($path, bool $strict = true);

    /**
     * {@inheritDoc}
     * @throws NestedAccessorException
     */
    public function set($path, $value, bool $strict = true): self;

    /**
     * {@inheritDoc}
     * @throws NestedAccessorException
     */
    public function append($path, $value, bool $strict = true): self;

    /**
     * {@inheritDoc}
     * @throws NestedAccessorException
     */
    public function delete($path, bool $strict = true): self;
}
