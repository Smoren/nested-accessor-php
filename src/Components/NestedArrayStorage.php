<?php

namespace Smoren\NestedAccessor\Components;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Factories\NestedAccessorFactory;
use Smoren\NestedAccessor\Interfaces\NestedAccessorInterface;

/**
 * Storage with nested accessor interface
 * @author Smoren <ofigate@gmail.com>
 */
class NestedArrayStorage implements NestedAccessorInterface
{
    /**
     * @var array<scalar, mixed> storage
     */
    protected array $storage;
    /**
     * @var NestedAccessorInterface nested accessor
     */
    protected NestedAccessorInterface $nestedAccessor;

    /**
     * @param array<scalar, mixed>|null $storage
     * @param NestedAccessorFactory|null $factory
     */
    public function __construct(?array $storage = null, ?NestedAccessorFactory $factory = null)
    {
        $this->storage = $storage ?? [];
        $this->nestedAccessor = ($factory ?? new NestedAccessorFactory())->fromArray($this->storage);
    }

    /**
     * @inheritDoc
     * @throws NestedAccessorException
     */
    public function get($path, bool $strict = true)
    {
        return $this->nestedAccessor->get($path, $strict);
    }

    /**
     * @inheritDoc
     * @throws NestedAccessorException
     */
    public function set($path, $value, bool $strict = true): self
    {
        $this->nestedAccessor->set($path, $value, $strict);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function append($path, $value, bool $strict = true): self
    {
        $this->nestedAccessor->append($path, $value, $strict);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, bool $strict = true): self
    {
        $this->nestedAccessor->delete($path, $strict);
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
