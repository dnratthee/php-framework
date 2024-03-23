<?php

namespace App\Libs\Helper;

class Arr
{
    public static function preg_array_key_exists($path, $array)
    {
        $keys = array_keys($array);
        foreach ($keys as $key) {
            if (count(explode("/", $key)) == count(explode("/", $path))) {

                $pattern = "/" . str_replace("/", "\/", $key) . "/i";
                if (preg_match($pattern, $path)) {
                    return [true, $key];
                }
            }
        }
        return [false, $key];
    }

    public static function getArrToKey($array)
    {
        $newArray = [];
        foreach ($array as $value) {
            $newArray[$value] = null;
        }
        return $newArray;
    }

    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }
}
