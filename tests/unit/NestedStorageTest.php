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
        $this->assertEquals(22, $ns->get(['a', 'd']));
    }
}
