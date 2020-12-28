<?php

declare(strict_types=1);

namespace Fernet;

class Params
{
    private static $objects = [];

    public static function component(array $params): string
    {
        $outputParams = [];
        foreach ($params as $key => $value) {
            $class = \get_class($value);
            $id = static::add($class, $value);
            $outputParams[] = "$key={{$id}}";
        }

        return implode(' ', $outputParams);
    }

    public static function add(string $key, $value)
    {
        $position = \count(static::$objects);
        $id = "$key#$position";
        static::$objects[$id] = $value;

        return $id;
    }

    public static function event(): string
    {
        $args = \func_get_args();
        $output = [];
        foreach ($args as $arg) {
            $output[] = serialize($arg);
        }

        return htmlentities(http_build_query(['fernet-params' => $output]));
    }

    public static function get(string $key)
    {
        return static::$objects[$key];
    }
}
