<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Helpers\NestedHelper;

class NestedHelperTest extends \Codeception\Test\Unit
{
    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testSimple()
    {
        $source = [
            'a' => ['b' => [['c' => 1], ['c' => 2]]],
        ];
        $this->assertEquals([1, 2], NestedHelper::get($source, ['a', 'b', 'c']));
        NestedHelper::set($source, ['a', 'd'], 22);
        $this->assertEquals([1, 2], NestedHelper::get($source, ['a', 'b', 'c']));
        $this->assertEquals(22, NestedHelper::get($source, ['a', 'd']));

        $source = [
            'test' => ['value' => 123],
        ];
        $this->assertEquals(123, NestedHelper::get($source, ['test', 'value']));
        $this->assertEquals(123, NestedHelper::get($source, 'test.value'));
        $this->assertEquals(null, NestedHelper::get($source, 'unknown.value', false));

        $source = ['test' => [1, 2]];
        NestedHelper::append($source, ['test'], 3);
        $this->assertEquals([1, 2, 3], NestedHelper::get($source, ['test']));
    }
}
