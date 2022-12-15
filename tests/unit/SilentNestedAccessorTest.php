<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Components\SilentNestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

class SilentNestedAccessorTest extends \Codeception\Test\Unit
{
    /**
     * @throws NestedAccessorException
     */
    public function testReadSimple()
    {
        $input = [
            'id' => 100,
            'name' => 'Novgorod',
            'status' => null,
            'country' => [
                'id' => 10,
                'name' => 'Russia',
                'friends' => ['Kazakhstan', 'Belarus', 'Armenia'],
                'capitals' => [
                    'msk' => 'Moscow',
                    'spb' => 'St. Petersburg',
                ],
            ],
            'streets' => [
                [
                    'id' => 1000,
                    'name' => 'Tverskaya',
                    'houses' => [1, 5, 9],
                ],
                [
                    'id' => 1002,
                    'name' => 'Leninskiy',
                    'houses' => [22, 35, 49],
                ],
                [
                    'id' => 1003,
                    'name' => 'Tarusskaya',
                    'houses' => [11, 12, 15],
                    'unknown' => 'some value',
                ],
            ],
            'msk_path' => 'country.capitals.msk',
        ];

        $accessor = new SilentNestedAccessor($input);

        $this->assertEquals('Novgorod', $accessor->get('name'));
        $this->assertEquals('Novgorod', $accessor->get(['name']));
        $this->assertEquals('Novgorod', $accessor->get('name'));
        $this->assertEquals(null, $accessor->get('status'));
        $this->assertEquals(null, $accessor->get('name1'));
    }

    /**
     * @throws NestedAccessorException
     */
    public function testWriteSimple()
    {
        $accessor = new SilentNestedAccessor($input);
        $accessor->set('test.a.a', 1);
        $this->assertEquals(['test' => ['a' => ['a' => 1]]], $accessor->get());
        $this->assertEquals(['test' => ['a' => ['a' => 1]]], $accessor->get(''));
        $this->assertEquals(['test' => ['a' => ['a' => 1]]], $accessor->get(null));
        $accessor->set('test.a.b', 2);
        $accessor->set('test.b.a', 3);
        $this->assertEquals(['a' => 1, 'b' => 2], $accessor->get('test.a'));
        $this->assertEquals(['a' => 3], $accessor->get('test.b'));
        $accessor->set('test.b.a', 33);
        $this->assertEquals(['a' => 33], $accessor->get('test.b'));
        $accessor->set('test.b.c', ['d' => 'e']);
        $this->assertEquals('e', $accessor->get('test.b.c.d'));
        $accessor->set('test.b', 0);
        $this->assertEquals(0, $accessor->get('test.b'));
        $this->assertEquals(null, $accessor->get('test.b.c.d', false));
        $accessor->set('test.b.c', (object)['d' => 'e']);
        $this->assertEquals((object)['d' => 'e'], $accessor->get('test.b.c', false));

        // silent skip
        $accessor->set('test.b.c.f', 123);

        $this->assertEquals('e', $accessor->get('test.b.c.d'));
        $accessor->set('test.b.c.f', 123, false);
        $this->assertEquals(123, $accessor->get('test.b.c.f'));
        $this->assertEquals(['a' => 1, 'b' => 2], $accessor->get('test.a'));

        $input = ['a' => 1];
        $accessor = new SilentNestedAccessor($input);
        $this->assertEquals(1, $accessor->get('a'));
        $this->assertEquals(['a' => 1], $accessor->get());
        $this->assertEquals(['a' => 1], $accessor->get(''));
        $accessor->set('a.b', 22);
        $this->assertEquals(['b' => 22], $accessor->get('a'));
        $accessor->set('c', 33);
        $this->assertEquals(['a' => ['b' => 22], 'c' => 33], $accessor->get());
    }

    public function testBadSource()
    {
        $input = 123;
        try {
            new SilentNestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertEquals(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertEquals('integer', $e->getData()['source_type']);
        }

        $input = 123.5;
        try {
            new SilentNestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertEquals(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertEquals('double', $e->getData()['source_type']);
        }

        $input = 'str';
        try {
            new SilentNestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertEquals(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertEquals('string', $e->getData()['source_type']);
        }

        $input = true;
        try {
            new SilentNestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertEquals(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertEquals('boolean', $e->getData()['source_type']);
        }
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testAppend()
    {
        $source = [
            'seq_array' => [1, 2],
            'ass_array' => ['a' => 1, 'b' => 2],
            'scalar' => 123,
        ];
        $na = new SilentNestedAccessor($source);

        $na->append('seq_array', 3);
        $this->assertEquals([1, 2, 3], $na->get('seq_array'));

        $na->append('seq_array', 5);
        $this->assertEquals([1, 2, 3, 5], $na->get('seq_array'));

        $na->append('ass_array', 11);
        $this->assertEquals(['a' => 1, 'b' => 2, 11], $na->get('ass_array'));

        $na->append('scalar', 22);
        $this->assertEquals([22], $na->get('scalar'));

        $source = [];
        $na = new SilentNestedAccessor($source);
        $na->append('', 1);
        $na->append(null, 2);
        $na->append([], 3);
        $this->assertEquals([1, 2, 3], $na->get(''));

        $source = [
            'a' => ['b' => [1, 2], 'c' => 5],
        ];
        $na = new SilentNestedAccessor($source);
        $na->append('a.b', 3);
        $this->assertEquals([1, 2, 3], $na->get('a.b'));

        $na->append('a.b.c', 6);
        $this->assertEquals([6], $na->get('a.b.c'));
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
        $na = new SilentNestedAccessor($source);

        $this->assertEquals(['a' => 1, 'b' => null, 'null' => 2], $na->get('test'));
        $na->delete('test.a');
        $this->assertEquals(['b' => null, 'null' => 2], $na->get('test'));

        $na->delete('test.b');
        $this->assertEquals(['null' => 2], $na->get('test'));

        $na->delete('test');
        $this->assertEquals(['null' => 3], $na->get(''));

        $na->delete('test');
        $this->assertEquals(['null' => 3], $na->get(''));

        $source = [
            'test' => (object)['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ];
        $na = new SilentNestedAccessor($source);

        $this->assertEquals((object)['a' => 1, 'b' => null, 'null' => 2], $na->get('test'));
        $na->delete('test.a');
        $this->assertEquals((object)['b' => null, 'null' => 2], $na->get('test'));

        $na->delete('test.b');
        $this->assertEquals((object)['null' => 2], $na->get('test'));

        $na->delete('test');
        $this->assertEquals(['null' => 3], $na->get(''));

        $source = [
            'test' => new class (10) {
                public int $a;

                public function __construct(int $a)
                {
                    $this->a = $a;
                }
            },
            'null' => 3,
        ];
        $na = new SilentNestedAccessor($source);

        // silent skip
        $na->delete('test.a');
        $na->delete('test.b');

        $this->assertEquals(10, $source['test']->a);
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
        $na = new SilentNestedAccessor($source);
        $this->assertTrue($na->exist('test.a'));
        $this->assertTrue($na->exist('test.b'));
        $this->assertTrue($na->exist('test.null'));
        $this->assertTrue($na->exist('null'));
        $this->assertFalse($na->exist('test.a.b'));
        $this->assertFalse($na->exist('test.c'));
        $this->assertFalse($na->exist('null.c'));
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
        $na = new SilentNestedAccessor($source);
        $this->assertTrue($na->isset('test.a'));
        $this->assertFalse($na->isset('test.b'));
        $this->assertTrue($na->isset('test.null'));
        $this->assertTrue($na->isset('null'));
        $this->assertFalse($na->isset('test.a.b'));
        $this->assertFalse($na->isset('test.c'));
        $this->assertFalse($na->isset('null.c'));
    }
}
