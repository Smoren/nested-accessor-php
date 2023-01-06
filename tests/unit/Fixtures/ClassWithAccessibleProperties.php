<?php

namespace Smoren\NestedAccessor\Tests\Unit\Fixtures;

class ClassWithAccessibleProperties
{
    public int $publicProperty = 1;
    public int $publicPropertyWithGetterAccess = 2;
    protected int $protectedProperty = 3;
    protected int $protectedPropertyWithGetterAccess = 4;
    private int $privateProperty = 5;
    private int $privatePropertyWithGetterAccess = 6;

    private function getPublicProperty(): int
    {
        return $this->publicProperty;
    }

    public function getPublicPropertyWithGetterAccess(): int
    {
        return $this->publicPropertyWithGetterAccess;
    }

    private function getProtectedProperty(): int
    {
        return $this->protectedProperty;
    }

    public function getProtectedPropertyWithGetterAccess(): int
    {
        return $this->protectedPropertyWithGetterAccess;
    }

    protected function getPrivateProperty(): int
    {
        return $this->privateProperty;
    }

    public function getPrivatePropertyWithGetterAccess(): int
    {
        return $this->privatePropertyWithGetterAccess;
    }
}
