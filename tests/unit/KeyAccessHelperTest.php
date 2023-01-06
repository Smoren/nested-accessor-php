<?php

namespace Smoren\NestedAccessor\Tests\Unit;

use ArrayAccess;
use ArrayObject;
use Codeception\Test\Unit;
use Smoren\NestedAccessor\Helpers\KeyAccessHelper;
use Smoren\NestedAccessor\Tests\Unit\Fixtures\ClassWithAccessibleProperties;
use stdClass;

class KeyAccessHelperTest extends Unit
{
    /**
     * @param array $input
     * @param string $key
     * @param bool $expected
     * @return void
     * @dataProvider existsInArrayDataProvider
     */
    public function testExistsInArray(array $input, string $key, bool $expected): void
    {
        // When
        $result = KeyAccessHelper::exists($input, $key);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function existsInArrayDataProvider(): array
    {
        return [
            [[], '', false],
            [[], '0', false],
            [[], 'a', false],
            [[], 'b', false],
            [['a' => 1, 'b' => 2], '', false],
            [['a' => 1, 'b' => 2], '0', false],
            [['a' => 1, 'b' => 2], '1', false],
            [['a' => 1, 'b' => 2], '2', false],
            [['a' => 1, 'b' => 2], 'a', true],
            [['a' => 1, 'b' => 2], 'b', true],
        ];
    }

    /**
     * @param array $input
     * @param string $key
     * @param mixed $defaultValue
     * @param mixed $expected
     * @return void
     * @dataProvider getFromArrayDataProvider
     */
    public function testGetFromArray(array $input, string $key, $defaultValue, $expected): void
    {
        // When
        $result = KeyAccessHelper::get($input, $key, $defaultValue);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function getFromArrayDataProvider(): array
    {
        return [
            [[], '', null, null],
            [[], '', 42, 42],
            [[], '0', null, null],
            [[], '0', 42, 42],
            [[], 'a', null, null],
            [[], 'b', 42, 42],
            [['a' => 1, 'b' => 2], '', null, null],
            [['a' => 1, 'b' => 2], '', 42, 42],
            [['a' => 1, 'b' => 2], '0', null, null],
            [['a' => 1, 'b' => 2], '0', 42, 42],
            [['a' => 1, 'b' => 2], '1', null, null],
            [['a' => 1, 'b' => 2], '1', 42, 42],
            [['a' => 1, 'b' => 2], '2', null, null],
            [['a' => 1, 'b' => 2], '2', 42, 42],
            [['a' => 1, 'b' => 2], 'a', 42, 1],
            [['a' => 1, 'b' => 2], 'b', 42, 2],
        ];
    }

    /**
     * @param ArrayAccess $input
     * @param string $key
     * @param bool $expected
     * @return void
     * @dataProvider existsInArrayAccessDataProvider
     */
    public function testExistsInArrayAccess(ArrayAccess $input, string $key, bool $expected): void
    {
        // When
        $result = KeyAccessHelper::exists($input, $key);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function existsInArrayAccessDataProvider(): array
    {
        $wrap = static function(array $input): ArrayAccess {
            return new ArrayObject($input);
        };

        return [
            [$wrap([]), '', false],
            [$wrap([]), '0', false],
            [$wrap([]), 'a', false],
            [$wrap([]), 'b', false],
            [$wrap(['a' => 1, 'b' => 2]), '', false],
            [$wrap(['a' => 1, 'b' => 2]), '0', false],
            [$wrap(['a' => 1, 'b' => 2]), '1', false],
            [$wrap(['a' => 1, 'b' => 2]), '2', false],
            [$wrap(['a' => 1, 'b' => 2]), 'a', true],
            [$wrap(['a' => 1, 'b' => 2]), 'b', true],
        ];
    }

    /**
     * @param ArrayAccess $input
     * @param string $key
     * @param mixed $defaultValue
     * @param mixed $expected
     * @return void
     * @dataProvider getFromArrayAccessDataProvider
     */
    public function testGetFromArrayAccess(ArrayAccess $input, string $key, $defaultValue, $expected): void
    {
        // When
        $result = KeyAccessHelper::get($input, $key, $defaultValue);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function getFromArrayAccessDataProvider(): array
    {
        $wrap = static function(array $input): ArrayAccess {
            return new ArrayObject($input);
        };

        return [
            [$wrap([]), '', null, null],
            [$wrap([]), '', 42, 42],
            [$wrap([]), '0', null, null],
            [$wrap([]), '0', 42, 42],
            [$wrap([]), 'a', null, null],
            [$wrap([]), 'b', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '0', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '0', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '1', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '1', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '2', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '2', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), 'a', 42, 1],
            [$wrap(['a' => 1, 'b' => 2]), 'b', 42, 2],
        ];
    }

    /**
     * @param stdClass $input
     * @param string $key
     * @param bool $expected
     * @return void
     * @dataProvider existsInStdClassDataProvider
     */
    public function testExistsInStdClass(stdClass $input, string $key, bool $expected): void
    {
        // When
        $result = KeyAccessHelper::exists($input, $key);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function existsInStdClassDataProvider(): array
    {
        $wrap = static function(array $input): object {
            return (object)$input;
        };

        return [
            [$wrap([]), '', false],
            [$wrap([]), '0', false],
            [$wrap([]), 'a', false],
            [$wrap([]), 'b', false],
            [$wrap(['a' => 1, 'b' => 2]), '', false],
            [$wrap(['a' => 1, 'b' => 2]), '0', false],
            [$wrap(['a' => 1, 'b' => 2]), '1', false],
            [$wrap(['a' => 1, 'b' => 2]), '2', false],
            [$wrap(['a' => 1, 'b' => 2]), 'a', true],
            [$wrap(['a' => 1, 'b' => 2]), 'b', true],
        ];
    }

    /**
     * @param stdClass $input
     * @param string $key
     * @param mixed $defaultValue
     * @param mixed $expected
     * @return void
     * @dataProvider getFromStdClassDataProvider
     */
    public function testGetFromStdClass(stdClass $input, string $key, $defaultValue, $expected): void
    {
        // When
        $result = KeyAccessHelper::get($input, $key, $defaultValue);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function getFromStdClassDataProvider(): array
    {
        $wrap = static function(array $input): object {
            return (object)$input;
        };

        return [
            [$wrap([]), '', null, null],
            [$wrap([]), '', 42, 42],
            [$wrap([]), '0', null, null],
            [$wrap([]), '0', 42, 42],
            [$wrap([]), 'a', null, null],
            [$wrap([]), 'b', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '0', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '0', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '1', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '1', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), '2', null, null],
            [$wrap(['a' => 1, 'b' => 2]), '2', 42, 42],
            [$wrap(['a' => 1, 'b' => 2]), 'a', 42, 1],
            [$wrap(['a' => 1, 'b' => 2]), 'b', 42, 2],
        ];
    }

    /**
     * @param object $input
     * @param string $key
     * @param bool $expected
     * @return void
     * @dataProvider existsInObjectDataProvider
     */
    public function testExistsInObject(object $input, string $key, bool $expected): void
    {
        // When
        $result = KeyAccessHelper::exists($input, $key);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function existsInObjectDataProvider(): array
    {
        return [
            [new ClassWithAccessibleProperties(), '', false],
            [new ClassWithAccessibleProperties(), '0', false],
            [new ClassWithAccessibleProperties(), 'unknownProperty', false],
            [new ClassWithAccessibleProperties(), 'publicProperty', true],
            [new ClassWithAccessibleProperties(), 'publicPropertyWithGetterAccess', true],
            [new ClassWithAccessibleProperties(), 'protectedProperty', false],
            [new ClassWithAccessibleProperties(), 'protectedPropertyWithGetterAccess', true],
            [new ClassWithAccessibleProperties(), 'privateProperty', false],
            [new ClassWithAccessibleProperties(), 'privatePropertyWithGetterAccess', true],
        ];
    }

    /**
     * @param object $input
     * @param string $key
     * @param mixed $defaultValue
     * @param mixed $expected
     * @return void
     * @dataProvider getFromObjectDataProvider
     */
    public function testGetFromObject(object $input, string $key, $defaultValue, $expected): void
    {
        // When
        $result = KeyAccessHelper::get($input, $key, $defaultValue);

        // Then
        $this->assertEquals($expected, $result);
    }

    public function getFromObjectDataProvider(): array
    {
        return [
            [new ClassWithAccessibleProperties(), '', null, null],
            [new ClassWithAccessibleProperties(), '', 42, 42],
            [new ClassWithAccessibleProperties(), '0', null, null],
            [new ClassWithAccessibleProperties(), '0', 42, 42],
            [new ClassWithAccessibleProperties(), 'unknownProperty', null, null],
            [new ClassWithAccessibleProperties(), 'unknownProperty', 42, 42],
            [new ClassWithAccessibleProperties(), 'publicProperty', null, 1],
            [new ClassWithAccessibleProperties(), 'publicProperty', 42, 1],
            [new ClassWithAccessibleProperties(), 'publicPropertyWithGetterAccess', null, 2],
            [new ClassWithAccessibleProperties(), 'publicPropertyWithGetterAccess', 42, 2],
            [new ClassWithAccessibleProperties(), 'protectedProperty', null, null],
            [new ClassWithAccessibleProperties(), 'protectedProperty', 42, 42],
            [new ClassWithAccessibleProperties(), 'protectedPropertyWithGetterAccess', null, 4],
            [new ClassWithAccessibleProperties(), 'protectedPropertyWithGetterAccess', 42, 4],
            [new ClassWithAccessibleProperties(), 'privateProperty', null, null],
            [new ClassWithAccessibleProperties(), 'privateProperty', 42, 42],
            [new ClassWithAccessibleProperties(), 'privatePropertyWithGetterAccess', null, 6],
            [new ClassWithAccessibleProperties(), 'privatePropertyWithGetterAccess', 42, 6],
        ];
    }

    /**
     * @param scalar $input
     * @param string $key
     * @return void
     * @dataProvider existsInScalarDataProvider
     */
    public function testExistsInScalar($input, string $key): void
    {
        // When
        $result = KeyAccessHelper::exists($input, $key);

        // Then
        $this->assertFalse($result);
    }

    public function existsInScalarDataProvider(): array
    {
        return [
            ['', ''],
            ['', '0'],
            ['', '1'],
            ['', '2'],
            [0, ''],
            [0, '0'],
            [0, '1'],
            [0, '2'],
            [1, ''],
            [1, '0'],
            [1, '1'],
            [1, '2'],
            ['0', ''],
            ['0', '0'],
            ['0', '1'],
            ['0', '2'],
            ['1', ''],
            ['1', '0'],
            ['1', '1'],
            ['1', '2'],
            ['111', ''],
            ['111', '0'],
            ['111', '1'],
            ['111', '2'],
        ];
    }

    /**
     * @param scalar $input
     * @param string $key
     * @param mixed $defaultValue
     * @return void
     * @dataProvider getFromScalarDataProvider
     */
    public function testGetFromScalar($input, string $key, $defaultValue): void
    {
        // When
        $result = KeyAccessHelper::get($input, $key, $defaultValue);

        // Then
        $this->assertEquals($defaultValue, $result);
    }

    public function getFromScalarDataProvider(): array
    {
        return [
            ['', '', null],
            ['', '', 42],
            ['', '0', null],
            ['', '0', 42],
            ['', '1', null],
            ['', '1', 42],
            ['', '2', null],
            ['', '2', 42],
            [0, '', null],
            [0, '', 42],
            [0, '0', null],
            [0, '0', 42],
            [0, '1', null],
            [0, '1', 42],
            [0, '2', null],
            [0, '2', 42],
            [1, '', null],
            [1, '', 42],
            [1, '0', null],
            [1, '0', 42],
            [1, '1', null],
            [1, '1', 42],
            [1, '2', null],
            [1, '2', 42],
            ['0', '', null],
            ['0', '', 42],
            ['0', '0', null],
            ['0', '0', 42],
            ['0', '1', null],
            ['0', '1', 42],
            ['0', '2', null],
            ['0', '2', 42],
            ['1', '', null],
            ['1', '', 42],
            ['1', '0', null],
            ['1', '0', 42],
            ['1', '1', null],
            ['1', '1', 42],
            ['1', '2', null],
            ['1', '2', 42],
            ['111', '', null],
            ['111', '', 42],
            ['111', '0', null],
            ['111', '0', 42],
            ['111', '1', null],
            ['111', '1', 42],
            ['111', '2', null],
            ['111', '2', 42],
        ];
    }
}
