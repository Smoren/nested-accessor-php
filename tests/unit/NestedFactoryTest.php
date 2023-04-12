<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Components\NestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Factories\NestedAccessorFactory;
use Smoren\NestedAccessor\Factories\SilentNestedAccessorFactory;

class NestedFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @throws NestedAccessorException
     */
    public function testExplicitFactory()
    {
        $sourceArray = ['test' => 1];
        $na = NestedAccessorFactory::create($sourceArray);
        $this->assertSame(1, $na->get('test'));

        $na = NestedAccessorFactory::fromArray($sourceArray);
        $this->assertSame(1, $na->get('test'));

        $sourceObject = (object)$sourceArray;
        $na = NestedAccessorFactory::fromObject($sourceObject);
        $this->assertSame(1, $na->get('test'));
    }

    /**
     * @return void
     * @throws NestedAccessorException
     */
    public function testSilentFactory()
    {
        $sourceArray = ['test' => 1];
        $na = SilentNestedAccessorFactory::create($sourceArray);
        $this->assertSame(1, $na->get('test'));

        $na = SilentNestedAccessorFactory::fromArray($sourceArray);
        $this->assertSame(1, $na->get('test'));

        $sourceObject = (object)$sourceArray;
        $na = SilentNestedAccessorFactory::fromObject($sourceObject);
        $this->assertSame(1, $na->get('test'));
    }
}
