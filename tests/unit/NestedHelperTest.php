<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Helpers\NestedAccess;

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
        $this->assertEquals([1, 2], NestedAccess::get($source, ['a', 'b', 'c']));
        NestedAccess::set($source, ['a', 'd'], 22);
        $this->assertEquals([1, 2], NestedAccess::get($source, ['a', 'b', 'c']));
        $this->assertEquals(22, NestedAccess::get($source, ['a', 'd']));

        $source = [
            'test' => ['value' => 123],
        ];
        $this->assertEquals(123, NestedAccess::get($source, ['test', 'value']));
        $this->assertEquals(123, NestedAccess::get($source, 'test.value'));
        $this->assertEquals(null, NestedAccess::get($source, 'unknown.value', false));

        $source = ['test' => [1, 2]];
        NestedAccess::append($source, ['test'], 3);
        $this->assertEquals([1, 2, 3], NestedAccess::get($source, ['test']));
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
        $this->assertTrue(NestedAccess::exist($source, 'test.a'));
        $this->assertTrue(NestedAccess::exist($source, 'test.b'));
        $this->assertTrue(NestedAccess::exist($source, 'test.null'));
        $this->assertTrue(NestedAccess::exist($source, 'null'));
        $this->assertFalse(NestedAccess::exist($source, 'test.a.b'));
        $this->assertFalse(NestedAccess::exist($source, 'test.c'));
        $this->assertFalse(NestedAccess::exist($source, 'null.c'));
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
        $this->assertTrue(NestedAccess::isset($source, 'test.a'));
        $this->assertFalse(NestedAccess::isset($source, 'test.b'));
        $this->assertTrue(NestedAccess::isset($source, 'test.null'));
        $this->assertTrue(NestedAccess::isset($source, 'null'));
        $this->assertFalse(NestedAccess::isset($source, 'test.a.b'));
        $this->assertFalse(NestedAccess::isset($source, 'test.c'));
        $this->assertFalse(NestedAccess::isset($source, 'null.c'));
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testDelete()
    {
        $source = [
            'test' => ['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ];

        $this->assertEquals(['a' => 1, 'b' => null, 'null' => 2], NestedAccess::get($source, 'test'));
        NestedAccess::delete($source, 'test.a');
        $this->assertEquals(['b' => null, 'null' => 2], NestedAccess::get($source, 'test'));

        NestedAccess::delete($source, 'test.b');
        $this->assertEquals(['null' => 2], NestedAccess::get($source, 'test'));

        NestedAccess::delete($source, 'test');
        $this->assertEquals(['null' => 3], NestedAccess::get($source, ''));

        try {
            NestedAccess::delete($source, 'test');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertEquals(NestedAccessorException::CANNOT_DELETE_VALUE, $e->getCode());
            $this->assertEquals('test', $e->getData()['path']);
        }
        $this->assertEquals(['null' => 3], NestedAccess::get($source, ''));

        NestedAccess::delete($source, 'test', false);
        $this->assertEquals(['null' => 3], NestedAccess::get($source, ''));
    }
}
