<?php

namespace App\Libs\Routing;

class RouteContainer
{
    protected $routes = [];

    public function add(Route $route)
    {
        $split_uri = explode("/", $route->uri);
        $param = [];
        foreach ($split_uri as $k => $p) {
            if (preg_match('/{(.*?)}/', $p, $matches)) {
                $param[$k] = $matches[1];
            }
        }
        $route->uri = preg_replace("/\{[\w]+\}/", "\S", $route->uri);
        $this->routes[$route->method][$route->uri] = ['middleware' => $route->middleware, 'controller' => $route->controller, 'action' => $route->action, 'param' => $route->param];
        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function hasRoute($method, $uri)
    {
        return isset($this->routes[$method][$uri]);
    }
}
