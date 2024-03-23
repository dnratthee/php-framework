<?php

namespace App\Libs\Routing;

use App\Libs\Container;

class Router extends Container
{
    protected $routes;

    public function __construct()
    {
        $this->routes = new RouteContainer();
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case "getRoutes":
                return $this->routes;
                break;
        }
        return $this->routes->$method(...$args);
    }
}
