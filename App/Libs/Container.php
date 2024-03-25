<?php

namespace App\Libs;

use App\Libs\Routing\Route;
use App\Libs\Routing\Router;
use App\Libs\Request;

class Container
{
    private static $instance;
    protected $instances = [];

    public function instance($key, $value)
    {
        $this->instances[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }
    }

    public static function setInstance($instance)
    {
        if (static::$instance) {
            return;
        }
        static::$instance = $instance;
    }

    public static function getInstance()
    {
        return static::$instance;
    }

    protected function register()
    {
        static::setInstance($this);
        $this->instance('Config', new Config);
        $this->instance('Router', new Router);
        $this->instance('Route', new Route);
        $this->instance('Request', new Request);
    }

    public static function __callStatic($method, $args)
    {
        $class = explode("\\", static::class);
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance->{end($class)}->$method(...$args);
    }
}
