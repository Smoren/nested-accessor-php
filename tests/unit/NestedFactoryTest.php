<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use Smoren\NestedAccessor\Components\NestedAccessor;
use Smoren\NestedAccessor\Exceptions\NestedAccessorException;
use Smoren\NestedAccessor\Factories\NestedAccessorFactory;

class NestedFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @throws NestedAccessorException
     */
    public function testFactory()
    {
        $sourceArray = [];
        NestedAccessorFactory::create($sourceArray);
        NestedAccessorFactory::fromArray($sourceArray);

        $sourceObject = (object)$sourceArray;
        NestedAccessorFactory::fromObject($sourceObject);
    }
}
