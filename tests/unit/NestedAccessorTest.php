<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Components\NestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;

class NestedAccessorTest extends \Codeception\Test\Unit
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

        $accessor = new NestedAccessor($input);

        $this->assertSame('Novgorod', $accessor->get('name'));
        $this->assertSame('Novgorod', $accessor->get(['name']));
        $this->assertSame('Novgorod', $accessor->get('name', true));
        $this->assertSame('Novgorod', $accessor->get('name', false));

        $this->assertNull($accessor->get('name1', false));

        try {
            $accessor->get('name1', true);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_GET_VALUE, $e->getCode());
            $this->assertSame('name1', $e->getData()['path']);
            $this->assertSame(1, $e->getData()['count']);
        }

        try {
            $accessor->get('name1');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_GET_VALUE, $e->getCode());
            $this->assertSame('name1', $e->getData()['path']);
            $this->assertSame(1, $e->getData()['count']);
        }

        try {
            $accessor->get('name1', true);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_GET_VALUE, $e->getCode());
            $this->assertSame('name1', $e->getData()['path']);
            $this->assertSame(1, $e->getData()['count']);
        }

        $this->assertNull($accessor->get('status'));
        $this->assertNull($accessor->get('status', true));
        $this->assertNull($accessor->get('status', false));

        $this->assertSame('Moscow', $accessor->get('country.capitals.msk'));
        $this->assertSame('Moscow', $accessor->get(['country', 'capitals', 'msk']));
        $this->assertNull($accessor->get('country.capitals.msk1', false));
        try {
            $accessor->get('country.capitals.msk1');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_GET_VALUE, $e->getCode());
            $this->assertSame('country.capitals.msk1', $e->getData()['path']);
            $this->assertSame(1, $e->getData()['count']);
        }
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testReadFromNonAssocArray()
    {
        $source = [
            ['data' => ['values' => [['value' => 1], ['value' => 2]]]],
            ['data' => ['values' => [['value' => 3]]]],
            ['data' => ['values' => [['value' => 4], ['value' => 5]]]],
        ];
        $accessor = new NestedAccessor($source);
        $this->assertEquals([1, 2, 3, 4, 5], $accessor->get('data.values.value'));

        $source = [
            ['data' => ['id' => 1]],
            ['data' => ['id' => 2]],
            ['data' => ['id' => 3]],
        ];
        $accessor = new NestedAccessor($source);
        $this->assertEquals([1, 2, 3], $accessor->get('data.id'));

        $source = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];
        $accessor = new NestedAccessor($source);
        $this->assertEquals([1, 2, 3], $accessor->get('id'));
    }

    public function testReadFromPropertyGetter()
    {
        $source = new class(5) {
            protected int $myProp;

            public function __construct(int $myProp)
            {
                $this->myProp = $myProp;
            }

            public function getMyProp(): int
            {
                return -$this->myProp;
            }
        };

        $accessor = new NestedAccessor($source);
        $this->assertSame(-5, $accessor->get('myProp'));

        $source = new class(5) {
            protected int $myProp;

            public function __construct(int $myProp)
            {
                $this->myProp = $myProp;
            }

            protected function getMyProp(): int
            {
                return -$this->myProp;
            }
        };

        $accessor = new NestedAccessor($source);
        try {
            $accessor->get('myProp');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame("cannot get value by path 'myProp'", $e->getMessage());
        }

        $source = new class(5) {
            private int $myProp;

            public function __construct(int $myProp)
            {
                $this->myProp = $myProp;
            }

            private function getMyProp(): int
            {
                return -$this->myProp;
            }
        };

        $accessor = new NestedAccessor($source);
        try {
            $accessor->get('myProp');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame("cannot get value by path 'myProp'", $e->getMessage());
        }
    }

    public function testReadFromClassObjectProperty()
    {
        $source = new class (10) {
            public int $a;

            public function __construct(int $a)
            {
                $this->a = $a;
            }
        };

        $accessor = new NestedAccessor($source);
        $this->assertSame(10, $accessor->get('a'));

        $source = new class (10) {
            public int $a;

            public function __construct(int $a)
            {
                $this->a = $a;
            }

            protected function getA()
            {
                return $this->a;
            }
        };

        $accessor = new NestedAccessor($source);
        $this->assertSame(10, $accessor->get('a'));

        $source = new class (10) {
            protected int $a;

            public function __construct(int $a)
            {
                $this->a = $a;
            }
        };

        $accessor = new NestedAccessor($source);
        try {
            $accessor->get('a');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame("cannot get value by path 'a'", $e->getMessage());
        }

        $source = new class (10) {
            private int $a;

            public function __construct(int $a)
            {
                $this->a = $a;
            }
        };

        $accessor = new NestedAccessor($source);
        try {
            $accessor->get('a');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame("cannot get value by path 'a'", $e->getMessage());
        }
    }

    /**
     * @throws NestedAccessorException
     */
    public function testReadWithFlattening()
    {
        $input = [
            'countries' => [
                [
                    'name' => 'Russia',
                    'cities' => [
                        [
                            'name' => 'Moscow',
                            'extra' => [
                                'codes' => [
                                    ['value' => 7495],
                                    ['value' => 7499],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Petersburg',
                            'extra' => [
                                'codes' => [
                                    ['value' => 7812],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Belarus',
                    'cities' => [
                        [
                            'name' => 'Minsk',
                            'extra' => [
                                'codes' => [
                                    ['value' => 375017],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $accessor = new NestedAccessor($input);
        $this->assertEquals(['Russia', 'Belarus'], $accessor->get('countries.name'));
        $this->assertEquals(['Moscow', 'Petersburg', 'Minsk'], $accessor->get('countries.cities.name'));
        $this->assertEquals([7495, 7499, 7812, 375017], $accessor->get('countries.cities.extra.codes.value'));

        $input = [
            'countries' => [
                [
                    'name' => 'Russia',
                    'cities' => [
                        [
                            'name' => 'Moscow',
                            'extra' => [
                                'codes' => [7495, 7499],
                            ],
                        ],
                        [
                            'name' => 'Petersburg',
                            'extra' => [
                                'codes' => [7812],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Belarus',
                    'cities' => [
                        [
                            'name' => 'Minsk',
                            'extra' => [
                                'codes' => [375017],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $accessor = new NestedAccessor($input);
        $this->assertEquals([[7495, 7499], [7812], [375017]], $accessor->get('countries.cities.extra.codes'));

        $input = [
            'countries' => [
                [
                    'name' => 'Russia',
                    'cities' => [
                        [
                            'name' => 'Moscow',
                        ],
                        [
                            'name' => 'Petersburg',
                            'extra' => [
                                'codes' => [
                                    ['value' => 7812],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Belarus',
                ],
                [
                    'name' => 'Kazakhstan',
                    'cities' => [
                        'extra' => [
                            'codes' => [
                                'value' => 123,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Armenia',
                    'cities' => [
                        'extra' => [
                            'codes' => 999,
                        ],
                    ],
                ],
                [
                    'name' => 'Serbia',
                    'cities' => [
                        'extra' => [
                            'codes' => [],
                        ],
                    ],
                ],
            ],
        ];

        $accessor = new NestedAccessor($input);
        $this->assertEquals([7812, 123], $accessor->get('countries.cities.extra.codes.value', false));

        try {
            $accessor->get('countries.cities.extra.codes.value');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_GET_VALUE, $e->getCode());
            $this->assertSame('countries.cities.extra.codes.value', $e->getData()['path']);
            $this->assertSame(3, $e->getData()['count']);
        }
    }

    /**
     * @throws NestedAccessorException
     */
    public function testReadObjects()
    {
        $input = (object)[
            'countries' => [
                [
                    'name' => 'Russia',
                    'cities' => [
                        (object)[
                            'name' => 'Moscow',
                            'extra' => [
                                'codes' => [
                                    ['value' => 7495],
                                    ['value' => 7499],
                                ],
                            ],
                        ],
                        [
                            'name' => 'Petersburg',
                            'extra' => [
                                'codes' => [
                                    ['value' => 7812],
                                ],
                            ],
                        ],
                    ],
                ],
                (object)[
                    'name' => 'Belarus',
                    'cities' => [
                        [
                            'name' => 'Minsk',
                            'extra' => [
                                'codes' => [
                                    ['value' => 375017],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        $accessor = new NestedAccessor($input);
        $this->assertEquals(['Russia', 'Belarus'], $accessor->get('countries.name'));
        $this->assertEquals(['Moscow', 'Petersburg', 'Minsk'], $accessor->get('countries.cities.name'));
        $this->assertEquals([7495, 7499, 7812, 375017], $accessor->get('countries.cities.extra.codes.value'));

        $input = (object)[
            'a' => 1,
            'b' => (object)[
                'c' => 2,
            ]
        ];
        $accessor = new NestedAccessor($input);
        $this->assertSame(1, $accessor->get('a'));
        $this->assertEquals((object)['c' => 2], $accessor->get('b'));
        $this->assertNull($accessor->get('c', false));
        $this->assertNull($accessor->get('c.d.e', false));
    }

    /**
     * @throws NestedAccessorException
     */
    public function testWriteSimple()
    {
        $accessor = new NestedAccessor($input);
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
        $this->assertSame('e', $accessor->get('test.b.c.d'));
        $accessor->set('test.b', 0);
        $this->assertSame(0, $accessor->get('test.b'));
        $this->assertNull($accessor->get('test.b.c.d', false));
        $accessor->set('test.b.c', (object)['d' => 'e']);
        $this->assertEquals((object)['d' => 'e'], $accessor->get('test.b.c', false));
        $accessor->set('test.b.c.f', 123);
        $this->assertSame(123, $accessor->get('test.b.c.f'));

        $this->assertSame('e', $accessor->get('test.b.c.d'));
        $accessor->set('test.b.c.f', 123, false);
        $this->assertSame(123, $accessor->get('test.b.c.f'));
        $this->assertEquals(['a' => 1, 'b' => 2], $accessor->get('test.a'));

        $input = ['a' => 1];
        $accessor = new NestedAccessor($input);
        $this->assertSame(1, $accessor->get('a'));
        $this->assertEquals(['a' => 1], $accessor->get());
        $this->assertEquals(['a' => 1], $accessor->get(''));
        $accessor->set('a.b', 22);
        $this->assertEquals(['b' => 22], $accessor->get('a'));
        $accessor->set('c', 33);
        $this->assertEquals(['a' => ['b' => 22], 'c' => 33], $accessor->get());

        $input = ['test' => (object)[]];
        $accessor = new NestedAccessor($input);
        $accessor->set('test.a', 123);
        $this->assertSame(123, $accessor->get('test.a'));

        $input = ['test' => new class (10) {
            public int $a;

            public function __construct(int $a)
            {
                $this->a = $a;
            }
        }];
        $accessor = new NestedAccessor($input);
        $accessor->set('test.a', 123);
        $this->assertSame(123, $accessor->get('test.a'));

        try {
            $accessor->set('test.b', 123);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_SET_VALUE, $e->getCode());
            $this->assertSame('test.b', $e->getData()['path']);
        }
    }

    public function testBadSource()
    {
        $input = 123;
        try {
            new NestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertSame('integer', $e->getData()['source_type']);
        }

        $input = 123.5;
        try {
            new NestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertSame('double', $e->getData()['source_type']);
        }

        $input = 'str';
        try {
            new NestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertSame('string', $e->getData()['source_type']);
        }

        $input = true;
        try {
            new NestedAccessor($input);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::SOURCE_IS_SCALAR, $e->getCode());
            $this->assertSame('boolean', $e->getData()['source_type']);
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
        $na = new NestedAccessor($source);

        $na->append('seq_array', 3);
        $this->assertEquals([1, 2, 3], $na->get('seq_array'));

        $na->append('seq_array', 5);
        $this->assertEquals([1, 2, 3, 5], $na->get('seq_array'));

        try {
            $na->append('ass_array', 11);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_APPEND_VALUE, $e->getCode());
            $this->assertSame('ass_array', $e->getData()['path']);
        }

        $na->append('ass_array', 11, false);
        $this->assertEquals(['a' => 1, 'b' => 2, 11], $na->get('ass_array'));

        try {
            $na->append('scalar', 33);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_APPEND_VALUE, $e->getCode());
            $this->assertSame('scalar', $e->getData()['path']);
        }

        $na->append('scalar', 22, false);
        $this->assertEquals([22], $na->get('scalar'));

        $source = [];
        $na = new NestedAccessor($source);
        $na->append('', 1);
        $na->append(null, 2);
        $na->append([], 3);
        $this->assertEquals([1, 2, 3], $na->get(''));

        $source = [
            'a' => ['b' => [1, 2], 'c' => 5],
        ];
        $na = new NestedAccessor($source);
        $na->append('a.b', 3);
        $this->assertEquals([1, 2, 3], $na->get('a.b'));

        try {
            $na->append('a.b.c', 6);
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_APPEND_VALUE, $e->getCode());
            $this->assertSame('a.b.c', $e->getData()['path']);
        }

        $na->append('a.b.c', 6, false);
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
        $na = new NestedAccessor($source);

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

        $source = [
            'test' => (object)['a' => 1, 'b' => null, 'null' => 2],
            'null' => 3,
        ];
        $na = new NestedAccessor($source);

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
        $na = new NestedAccessor($source);

        try {
            $na->delete('test.a');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_DELETE_VALUE, $e->getCode());
            $this->assertSame('test.a', $e->getData()['path']);
        }

        try {
            $na->delete('test.b');
            $this->fail();
        } catch(NestedAccessorException $e) {
            $this->assertSame(NestedAccessorException::CANNOT_DELETE_VALUE, $e->getCode());
            $this->assertSame('test.b', $e->getData()['path']);
        }
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
        $na = new NestedAccessor($source);
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
        $na = new NestedAccessor($source);
        $this->assertTrue($na->isset('test.a'));
        $this->assertFalse($na->isset('test.b'));
        $this->assertTrue($na->isset('test.null'));
        $this->assertTrue($na->isset('null'));
        $this->assertFalse($na->isset('test.a.b'));
        $this->assertFalse($na->isset('test.c'));
        $this->assertFalse($na->isset('null.c'));
    }
}
