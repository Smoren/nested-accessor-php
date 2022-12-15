<?php

namespace Smoren\NestedAccessor\Components;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Interfaces\SilentNestedAccessorInterface;

class SilentNestedAccessor implements SilentNestedAccessorInterface
{
    /**
     * @var NestedAccessor
     */
    protected NestedAccessor $nestedAccessor;

    /**
     * ArrayNestedAccessor constructor.
     * @param array<scalar, mixed>|object $source
     * @param non-empty-string $pathDelimiter
     * @throws NestedAccessorException
     */
    public function __construct(&$source, string $pathDelimiter = '.')
    {
        $this->nestedAccessor = new NestedAccessor($source, $pathDelimiter);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path = null)
    {
        return $this->nestedAccessor->get($path, false);
    }

    /**
     * {@inheritDoc}
     */
    public function set($path, $value): self
    {
        $this->nestedAccessor->set($path, $value, false);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function append($path, $value): self
    {
        $this->nestedAccessor->append($path, $value, false);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path): self
    {
        $this->nestedAccessor->delete($path, false);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function exist($path): bool
    {
        return $this->nestedAccessor->exist($path);
    }

    /**
     * {@inheritDoc}
     */
    public function isset($path): bool
    {
        return $this->nestedAccessor->isset($path);
    }
}
