<?php

namespace App\Framework\Value;

use ReflectionClass;

trait EnumTrait
{
    public static function getEnumValues() : array
    {
        return array_merge(
            (new ReflectionClass(self::class))->getConstants(),
            (new ReflectionClass(static::class))->getConstants(),
        );
    }
}
