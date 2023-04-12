<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Components\NestedArrayStorage;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

class NestedStorageTest extends \Codeception\Test\Unit
{
    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testSimple()
    {
        $ns = new NestedArrayStorage([
            'a' => ['b' => [['c' => 1], ['c' => 2]]],
        ]);
        $this->assertEquals([1, 2], $ns->get(['a', 'b', 'c']));
        $ns->set(['a', 'd'], 22);
        $this->assertEquals([1, 2], $ns->get(['a', 'b', 'c']));
        $this->assertSame(22, $ns->get(['a', 'd']));

        $ns = new NestedArrayStorage(['test' => [1, 2]]);
        $ns->append('test', 3);
        $this->assertEquals([1, 2, 3], $ns->get(['test']));
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testExist()
    {
        $ns = new NestedArrayStorage([
            'test' => ['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ]);
        $this->assertTrue($ns->exist('test.a'));
        $this->assertTrue($ns->exist('test.b'));
        $this->assertTrue($ns->exist('test.null'));
        $this->assertTrue($ns->exist('null'));
        $this->assertFalse($ns->exist('test.a.b'));
        $this->assertFalse($ns->exist('test.c'));
        $this->assertFalse($ns->exist('null.c'));
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testIsset()
    {
        $ns = new NestedArrayStorage([
            'test' => ['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ]);
        $this->assertTrue($ns->isset('test.a'));
        $this->assertFalse($ns->isset('test.b'));
        $this->assertTrue($ns->isset('test.null'));
        $this->assertTrue($ns->isset('null'));
        $this->assertFalse($ns->isset('test.a.b'));
        $this->assertFalse($ns->isset('test.c'));
        $this->assertFalse($ns->isset('null.c'));
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testDelete()
    {
        $na = new NestedArrayStorage([
            'test' => ['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ]);

        $this->assertEquals(['a' => 1, 'b' => null, 'null' => 2], $na->get('test'));
        $na->delete('test.a');
        $this->assertEquals(['b' => null, 'null' => 2], $na->get('test'));

        $na->delete('test.b');
        $this->assertEquals(['null' => 2], $na->get('test'));

        $na->delete('test');
        $this->assertEquals(['null' => 3], $na->get(''));

        try {
            $na->delete('test');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_DELETE_VALUE, $e->getCode());
            $this->assertSame('test', $e->getData()['path']);
        }
        $this->assertEquals(['null' => 3], $na->get(''));

        $na->delete('test', false);
        $this->assertEquals(['null' => 3], $na->get(''));
    }
}
