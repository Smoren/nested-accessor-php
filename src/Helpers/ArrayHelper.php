<?php

namespace Smoren\NestedAccessor\Helpers;

/**
 * Helper for detecting array type
 * @author Smoren <ofigate@gmail.com>
 */
class ArrayHelper
{
    /**
     * Returns true if array is associative else false
     * @param array<mixed> $input array to check
     * @return bool result flag
     */
    public static function isAssoc(array $input): bool
    {
        if([] === $input) {
            return false;
        }
        return array_keys($input) !== range(0, count($input) - 1);
    }
}
