<?php

namespace App\Helpers;

class NumberChecker
{
    /**
     * tell if number is really betwenn specified numbers.
     *
     * @throws InvalidArgumentException
     */
    public static function isBetween(int $number, int $min, int $max)
    {
        if ($min <= $number && $number <= $max) {
            return true;
        }
        throw new \InvalidArgumentException(
            "Number {$number} should be set between {$min} and {$max}"
        );
    }
}
