<?php

declare(strict_types=1);

namespace Fernet;

class Params
{
    /**
     * @var array Objects are saving here so we can pass them as text
     */
    private static array $objects = [];

    /**
     * Prints the dynamic params passed to the component
     *
     * @param array $params
     * @return string
     */
    public static function component(array $params): string
    {
        $outputParams = [];
        foreach ($params as $key => $value) {
            $class = \get_class($value);
            $name = static::set($class, $value);
            $outputParams[] = "$key={{$name}}";
        }
        return implode(' ', $outputParams);
    }

    /**
     * Prints the dynamic values passed to the events
     */
    public static function event(): string
    {
        $args = \func_get_args();
        $output = [];
        foreach ($args as $arg) {
            $output[] = serialize($arg);
        }
        return htmlentities(http_build_query(['fernet-params' => $output]));
    }

    public static function set(string $key, $value): string
    {
        $position = \count(static::$objects);
        $name = "$key#$position";
        static::$objects[$name] = $value;

        return $name;
    }

    public static function get(string $key)
    {
        return static::$objects[$key];
    }

}
