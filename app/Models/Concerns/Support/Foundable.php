<?php

namespace App\Models\Concerns\Support;

/**
 * @property array $list
 */
trait Foundable
{
    public static function getIntByString(string $key): ?int
    {
        return static::$list[$key] ?? null;
    }

    public static function getStringByInt(int $key): ?string
    {
        return array_flip(static::$list)[$key] ?? null;
    }
}
