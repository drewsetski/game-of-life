<?php declare(strict_types=1);

namespace App\GameOfLife\Helpers;

class Arr
{
    public static function get(array $array, $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    public static function first(array $array, $default = null)
    {
        return reset($array) ?? $default;
    }
}
