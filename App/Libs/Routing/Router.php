<?php

namespace App\Libs\Routing;

use App\Libs\Container;

/**
 * @method static \App\Libs\Routing\RouteContainer add(\App\Libs\Routing\Route $route)
 * @method static \App\Libs\Routing\RouteContainer getRoutes()
 * @method static \App\Libs\Routing\RouteContainer hasRoute(string $method, string $uri)
 * 
 */

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
