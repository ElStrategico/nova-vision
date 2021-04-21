<?php

namespace NovaVision\Helpers;


class ArrayHelper
{
    /**
     * @param array $array
     * @param callable $callback
     * @return array
     */
    public static function replace(array $array, callable $callback)
    {
        $result = [];
        foreach($array as $value)
        {
            $result[] = $callback($value);
        }

        return $result;
    }

    public static function replaceAllWith(array $array, $replacement)
    {
        return self::replace($array, function ($value) use ($replacement) {
            return $replacement;
        });
    }
}