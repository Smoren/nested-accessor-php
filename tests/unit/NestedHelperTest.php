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

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testExist()
    {
        $source = [
            'test' => ['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ];
        $this->assertTrue(NestedHelper::exist($source, 'test.a'));
        $this->assertTrue(NestedHelper::exist($source, 'test.b'));
        $this->assertTrue(NestedHelper::exist($source, 'test.null'));
        $this->assertTrue(NestedHelper::exist($source, 'null'));
        $this->assertFalse(NestedHelper::exist($source, 'test.a.b'));
        $this->assertFalse(NestedHelper::exist($source, 'test.c'));
        $this->assertFalse(NestedHelper::exist($source, 'null.c'));
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testIsset()
    {
        $source = [
            'test' => ['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ];
        $this->assertTrue(NestedHelper::isset($source, 'test.a'));
        $this->assertFalse(NestedHelper::isset($source, 'test.b'));
        $this->assertTrue(NestedHelper::isset($source, 'test.null'));
        $this->assertTrue(NestedHelper::isset($source, 'null'));
        $this->assertFalse(NestedHelper::isset($source, 'test.a.b'));
        $this->assertFalse(NestedHelper::isset($source, 'test.c'));
        $this->assertFalse(NestedHelper::isset($source, 'null.c'));
    }
}
