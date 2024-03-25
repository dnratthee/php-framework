<?php

namespace App\Libs;

use App\Libs\Helper\Arr;

class Config extends Container
{
    protected $config = [];

    protected function load()
    {
        include __DIR__ . '/Helper/helpers.php';

        $this->config['app'] = include __DIR__ . '/../../config/app.php';
        $this->config['db'] = include __DIR__ . '/../../config/database.php';

        if ($this->config['app']['debug'] == true) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }

        return $this;
    }

    public function __get($key)
    {
        return Arr::get($this->config, $key);
    }

    public function __set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case "boot":
                return $this->load();
                break;
            case "get":
                return Arr::get($this->config, $args[0]);
                break;
            case "set":
                $this->config[$args[0]] = $args[1];
                break;
        }
    }
}
