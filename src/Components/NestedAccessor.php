<?php

namespace Smoren\NestedAccessor\Components;

use Smoren\NestedAccessor\Helpers\ArrayHelper;
use Smoren\NestedAccessor\Interfaces\NestedAccessorInterface;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\TypeTools\MapAccess;
use Smoren\TypeTools\ObjectAccess;
use stdClass;

/**
 * Accessor class for getting and setting to source array or object with nested keys
 * @author Smoren <ofigate@gmail.com>
 */
class NestedAccessor implements NestedAccessorInterface
{
    public const SET_MODE_SET = 1;
    public const SET_MODE_APPEND = 2;
    public const SET_MODE_DELETE = 3;

    /**
     * @var array<int|string, mixed>|object data source for accessing
     */
    protected $source;
    /**
     * @var non-empty-string path's separator of nesting
     */
    protected string $pathDelimiter;

    /**
     * ArrayNestedAccessor constructor.
     * @param array<scalar, mixed>|object $source
     * @param non-empty-string $pathDelimiter
     * @throws NestedAccessorException
     */
    public function __construct(&$source, string $pathDelimiter = '.')
    {
        $this->setSource($source);
        $this->pathDelimiter = $pathDelimiter;
    }

    /**
     * Setter for source
     * @param array<scalar, mixed>|object $source source setter
     * @return void
     * @throws NestedAccessorException
     */
    public function setSource(&$source): void
    {
        /** @var array<scalar, mixed>|object|mixed|null $source */
        if($source === null) {
            $source = [];
        }

        if(is_scalar($source)) {
            throw NestedAccessorException::createAsSourceIsScalar($source);
        }

        /** @var array<int|string, mixed>|object $source */
        $this->source = &$source;
    }

    /**
     * {@inheritDoc}
     */
    public function get($path = null, bool $strict = true)
    {
        // when path is not specified
        if($path === null || $path === '') {
            // let's return the full source
            return $this->source;
        }

        $path = $this->formatPath($path);

        // let result be null and there are no errors by default
        $result = null;
        $errorsCount = 0;

        // getting result with internal recursive method
        $this->_get(
            $this->source,
            array_reverse($path), // path stack
            $result,
            $errorsCount
        );

        // when strict mode is on and we got errors
        if($strict && $errorsCount) {
            throw NestedAccessorException::createAsCannotGetValue(
                implode($this->pathDelimiter, $path),
                $errorsCount
            );
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function set($path, $value, bool $strict = true): self
    {
        $path = $this->formatPath($path);
        return $this->_set($this->source, $path, $value, self::SET_MODE_SET, $strict);
    }

    /**
     * {@inheritDoc}
     */
    public function append($path, $value, bool $strict = true): self
    {
        $path = $this->formatPath($path);
        return $this->_set($this->source, $path, $value, self::SET_MODE_APPEND, $strict);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, bool $strict = true): self
    {
        $path = $this->formatPath($path);

        if(!$this->exist($path)) {
            if($strict) {
                throw NestedAccessorException::createAsCannotSetValue(
                    self::SET_MODE_DELETE,
                    implode($this->pathDelimiter, $path)
                );
            }
            return $this;
        }

        return $this->_set($this->source, $path, null, self::SET_MODE_DELETE, $strict);
    }

    /**
     * {@inheritDoc}
     */
    public function exist($path): bool
    {
        try {
            $this->get($path);
            return true;
        } catch(NestedAccessorException $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isset($path): bool
    {
        try {
            return $this->get($path) !== null;
        } catch(NestedAccessorException $e) {
            return false;
        }
    }

    /**
     * Internal recursive method to get value from source by path stack
     * @param mixed $source source to get value from
     * @param array<string> $path nested path stack
     * @param array<scalar, mixed>|mixed $result place for result
     * @param int $errorsCount errors counter
     * @return void
     */
    protected function _get($source, array $path, &$result, int &$errorsCount): void
    {
        // let's iterate every path part from stack
        while(count($path)) {
            if(is_array($source) && $this->isLastKeyInteger($path)) {
                $key = array_pop($path);

                if (!array_key_exists($key, $source)) {
                    $errorsCount++;
                    return;
                }

                $source = $source[$key];

                continue;
            }

            if(is_array($source) && !ArrayHelper::isAssoc($source)) {
                // the result will be multiple
                if(!is_array($result)) {
                    $result = [];
                }
                // and we need to use recursive call for each item of this array
                foreach($source as $item) {
                    $this->_get($item, $path, $result, $errorsCount);
                }
                // we don't need to do something in this recursive branch
                return;
            }

            $key = array_pop($path);

            if(MapAccess::exists($source, $key)) {
                // go to the next nested level
                $source = MapAccess::get($source, $key);
            } else {
                // path part key is missing in source object
                $errorsCount++;
                // we cannot go deeper
                return;
            }

            // when it's not the last iteration of the stack
            // and the source is non-associative array (list)
            /** @var mixed $source */
            if(count($path) && is_array($source) && !ArrayHelper::isAssoc($source)) {
                if(is_array($source) && $this->isLastKeyInteger($path)) {
                    continue;
                }

                // the result will be multiple
                if(!is_array($result)) {
                    $result = [];
                }
                // and we need to use recursive call for each item of this array
                foreach($source as $item) {
                    $this->_get($item, $path, $result, $errorsCount);
                }
                // we don't need to do something in this recursive branch
                return;
            }
        }

        // now path stack is empty — we reached target value of given path in source argument
        // so if result is multiple
        if(is_array($result)) {
            // we append source to result
            $result[] = $source;
        } else {
            // result is single
            $result = $source;
        }
        // that's all folks!
    }

    /**
     * Internal recursive method to save value to source by path stack
     * @param array<scalar, mixed>|object $source source to save value to
     * @param array<string> $path nested path
     * @param mixed $value value to save to source
     * @param int $mode when true append or set
     * @param bool $strict when true throw exception if path not exist in source object
     * @return $this
     * @throws NestedAccessorException
     */
    protected function _set(&$source, array $path, $value, int $mode, bool $strict): self
    {
        $temp = &$source;
        $tempPrevSource = null;
        $tempPrevKey = null;

        // let's iterate every path part to go deeper into nesting
        foreach($path as $key) {
            if(isset($temp) && is_scalar($temp)) {
                // value in the middle of the path must be an array
                $temp = [];
            }

            $tempPrevSource = &$temp;
            $tempPrevKey = $key;

            // go to the next nested level
            if(is_object($temp)) {
                if($strict && !($temp instanceof stdClass) && !ObjectAccess::hasPublicProperty($temp, $key)) {
                    throw NestedAccessorException::createAsCannotSetValue($mode, implode($this->pathDelimiter, $path));
                }
                $temp = &$temp->{$key};
            } else {
                // TODO check PHPStan: "Cannot access offset string on mixed"
                /** @var array<string, mixed> $temp */
                $temp = &$temp[$key];
            }
        }
        // now we can save value to the source
        switch($mode) {
            case self::SET_MODE_SET:
                $temp = $value;
                break;
            case self::SET_MODE_APPEND:
                $this->_append($temp, $value, $path, $strict);
                break;
            case self::SET_MODE_DELETE:
                if(!$this->_delete($tempPrevSource, $tempPrevKey, $path, $strict)) {
                    return $this;
                }
                break;
        }
        unset($temp);

        return $this;
    }

    /**
     * Appends value to source.
     * @param mixed $source source to append value to
     * @param mixed $value value to append to source
     * @param array<string> $path nested path
     * @param bool $strict if true: throw exception when cannot append value
     * @return void
     * @throws NestedAccessorException if cannot append item to source (in strict mode only)
     */
    protected function _append(&$source, $value, array $path, bool $strict): void
    {
        if(!is_array($source) || ArrayHelper::isAssoc($source)) {
            if($strict) {
                throw NestedAccessorException::createAsCannotSetValue(
                    self::SET_MODE_APPEND,
                    implode($this->pathDelimiter, $path)
                );
            } elseif(!is_array($source)) {
                $source = [];
            }
        }

        $source[] = $value;
    }

    /**
     * Removes item from source.
     * @param mixed $source source to remove item from
     * @param string|null $key key to remove item from source by
     * @param array<string> $path nested path
     * @param bool $strict if true: throw exception when cannot remove item
     * @return bool true if removing is succeeded
     * @throws NestedAccessorException if item to remove is not exists (in strict mode only)
     */
    protected function _delete(&$source, $key, array $path, bool $strict): bool
    {
        if($key === null || (!is_array($source) && !($source instanceof stdClass))) {
            if($strict) {
                throw NestedAccessorException::createAsCannotSetValue(
                    self::SET_MODE_DELETE,
                    implode($this->pathDelimiter, $path)
                );
            } else {
                return false;
            }
        }
        if(is_array($source)) {
            unset($source[$key]);
        } else {
            unset($source->{$key});
        }

        return true;
    }

    /**
     * @param string|string[]|null $path
     * @return string[]
     */
    protected function formatPath($path): array
    {
        if(is_array($path)) {
            return $path;
        }

        if($path === null || $path === '') {
            return [];
        }

        return explode($this->pathDelimiter, $path);
    }

    /**
     * @param array<string> $path
     * @return bool
     */
    protected function isLastKeyInteger(array $path): bool
    {
        $lastKey = $path[count($path)-1];
        return boolval(preg_match('/^[0-9]+$/', $lastKey));
    }
}
