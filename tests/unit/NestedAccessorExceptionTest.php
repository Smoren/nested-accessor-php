<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Components\NestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

class NestedAccessorExceptionTest extends \Codeception\Test\Unit
{
    public function testUnknownMode()
    {
        $e = NestedAccessorException::createAsCannotSetValue(NestedAccessor::SET_MODE_SET, 'test');
        $this->assertSame(NestedAccessorException::CANNOT_SET_VALUE, $e->getCode());

        $e = NestedAccessorException::createAsCannotSetValue(NestedAccessor::SET_MODE_APPEND, 'test');
        $this->assertSame(NestedAccessorException::CANNOT_APPEND_VALUE, $e->getCode());

        $e = NestedAccessorException::createAsCannotSetValue(NestedAccessor::SET_MODE_DELETE, 'test');
        $this->assertSame(NestedAccessorException::CANNOT_DELETE_VALUE, $e->getCode());

        $e = NestedAccessorException::createAsCannotSetValue(-1, 'test');
        $this->assertSame(NestedAccessorException::UNKNOWN_SET_MODE, $e->getCode());
    }
}
