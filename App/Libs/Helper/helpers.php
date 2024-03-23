<?php

use App\Libs\Container;
use App\Libs\Helper\Env;

if (!function_exists('env')) {

    // Get the value of an environment variable or return a default value
    function env($key, $default = null)
    {
        $env = new Env();
        return $env->get($key, $default);
    }
}

if (!function_exists('debug')) {

    function debug($message, $type = 1)
    {
        if (env('APP_DEBUG')) {
            header('Content-Type: text/html');
            echo "<pre>";
            if ($type == 1) {
                print_r($message);
            } else {
                var_dump($message);
            }
            echo "</pre>";
            die();
        }
    }
}

if (!function_exists('camelToSnake')) {

    function camelToSnake($camelCase)
    {
        $result = '';

        for ($i = 0; $i < strlen($camelCase); $i++) {
            $char = $camelCase[$i];

            if (ctype_upper($char)) {
                $result .= '_' . strtolower($char);
            } else {
                $result .= $char;
            }
        }

        return ltrim($result, '_');
    }
}

if (!function_exists('getClassName')) {

    function getClassName($class, $type = 'snake')
    {
        $className = get_class($class);
        $className = explode('\\', $className);
        $className = end($className);
        if ($type == 'snake') {
            return camelToSnake($className);
        }
        return $className;
    }
}

if (!function_exists('app')) {
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}


if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}
