<?php

namespace App\Libs\Routing;

use App\Libs\Application;
use App\Libs\Container;

/**
 * @method static \App\Libs\Routing\Route middleware(array|string|null $middleware)
 * @method static \App\Libs\Routing\Route controller(string $controller)
 * @method static \App\Libs\Routing\Route get(string $path, array|string|callable|null $action = null)
 * @method static \App\Libs\Routing\Route post(string $path, array|string|callable|null $action = null)
 * @method static \App\Libs\Routing\Route put(string $path, array|string|callable|null $action = null)
 * @method static \App\Libs\Routing\Route delete(string $path, array|string|callable|null $action = null)
 * @method static \App\Libs\Routing\RouteController get(string $path, array|string|callable|null $action = null)
 * 
 */

class Route extends Container
{
    public $uri;
    public $method;
    public $action;
    public $controller;
    public $middleware;
    public $prefix = [];
    public $param;

    public function __construct()
    {
        //
    }

    public function controller($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    public function addRoute($route, $action, $method)
    {
        $routes = Application::getInstance()->Router->getRoutes();
        $d = $routes->add($this->createRoute($route, $action, $method));
        $this->clear();
        return $d;
    }

    public function createRoute($route, $action, $method)
    {
        $routes = Application::getInstance()->Router->getRoutes();
        $this->uri = $route;
        $this->method = $method;

        if (isset($this->prefix)) {
            $this->uri = implode($this->prefix) . $this->uri;
        }

        $split_uri = explode("/", $this->uri);
        foreach ($split_uri as $k => $p) {
            if (preg_match('/{(.*?)}/', $p, $matches)) {
                $this->param[$k] = $matches[1];
            }
        }

        $this->uri = preg_replace("/\{[\w]+\}/", "\S", $this->uri);

        if ($routes->hasRoute($method, $this->uri)) {
            http_response_code(500);
            debug("Route " . $method . " " . $route . " already exists");
            exit;
        }

        if (is_string($action)) {
            $this->action = $action;
        } else {
            $this->controller = $action[0];
            $this->action = $action[1];
        }
        return $this;
    }

    public function prefix(string $prefix)
    {
        array_push($this->prefix, $prefix);
        return $this;
    }

    public function group($call)
    {
        if (is_callable($call)) {
            $call($this);
            $this->pop();
        } else {
            include realpath($call);
        }
    }

    private function clear()
    {
        $this->uri = null;
        $this->method = null;
        $this->action = null;
        $this->param = null;
    }

    private function pop()
    {
        array_pop($this->prefix);
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case "get":
            case "post":
            case "put":
            case "delete":
                $this->addRoute($args[0], $args[1], strtoupper($method));
                break;
            case "middleware":
                $this->prefix = [];
                $this->middleware = $args[0];
                return $this;
                break;
        }
    }
}
