<?php

namespace App\Libs\Helper;

class Autoload
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $class = str_replace('\\', '/', $class);
            $path = str_replace('/App/Libs/Helper', '', __DIR__);
            $file = $path . "/$class.php";
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}

Autoload::register();
