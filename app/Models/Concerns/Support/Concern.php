<?php

namespace App\Models\Concerns\Support;

abstract class Concern
{
    abstract public static function getIntByString(string $key): ?int;

    abstract public static function getStringByInt(int $key): ?string;
}
