<?php

namespace App\Libs;

use App\Libs\Helper\Arr;

/**
 * 
 * @method static App\Libs\Request isApi()
 * @method static App\Libs\Request capture(\App\Libs\Routing\RouteContainer $container)
 * 
 */

class Request extends Container
{
    protected $uri;
    protected $method;
    protected $params = [];
    protected $json = [];
    protected $body = [];

    protected $middleware;
    protected $controller;
    protected $action;

    public function __construct(...$args)
    {
        $this->input(...$args);
    }

    public static function uri()
    {
        return strtok($_SERVER['REQUEST_URI'], '?');
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function input(...$args)
    {
        foreach ($args as $key => $value) {
            $this->params[$key] = $value;
        }

        foreach ($_GET as $key => $value) {
            try {
                if (is_string($value)) $this->params[$key] = json_decode($value, true);
                else $this->params[$key] = $value;
            } catch (\Exception $e) {
                $this->params[$key] = $value;
            }
        }

        $this->json = json_decode(file_get_contents('php://input'), true);
        if ($this->json) {
            foreach ($this->json as $key => $value) {
                $this->body[$key] = $value;
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {
                $this->body[$key] = $value;
            }
        }
        return $this;
    }

    public static function header($key)
    {
        return getallheaders()[$key] ?? null;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function all()
    {
        return $this->body;
    }

    public function __get($key)
    {
        return $this->params[$key] ?? $this->body[$key] ?? null;
    }

    public function sCapture(\App\Libs\Routing\RouteContainer $container)
    {
        $router = $container->getRoutes();
        $this->uri = strtok($_SERVER['REQUEST_URI'], '?');
        $this->method =  $_SERVER['REQUEST_METHOD'];

        try {
            if (!array_key_exists($this->method, $router)) {
                http_response_code(405);
                echo "Method not allowed";
                exit;
            }

            $routes = $router[$this->method];

            if (array_key_exists($this->uri, $routes)) {
                $this->middleware = $router[$this->method][$this->uri]['middleware'];
                $this->controller = $router[$this->method][$this->uri]['controller'];
                $action = $router[$this->method][$this->uri]['action'];
                $this->action = $action;
                $controller = new $this->controller();
                $req = new Request($controller, $action);
                $controller->$action($req);
                return $req;
            } else if (Arr::preg_array_key_exists($this->uri, $routes)[0]) {
                $path = Arr::preg_array_key_exists($this->uri, $routes)[1];
                if (!$router[$this->method][$path]['param']) {
                    http_response_code(404);
                    echo "404 Not Found";
                    return;
                }

                $data = [];
                foreach ($router[$this->method][$path]['param'] as $k => $p) {
                    $data[$p] = explode("/", $this->uri)[$k];
                }
                $this->middleware = $router[$this->method][$path]['middleware'];
                $this->controller = $router[$this->method][$path]['controller'];
                $action = $router[$this->method][$path]['action'];
                $this->action = $action;
                $controller = new $this->controller();
                $req = new Request($controller, $action, ...$data);
                $controller->$action($req);
                return $req;
            } else {
                http_response_code(404);
                echo "404 Not Found";
                return;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return;
        }
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case 'isApi':
                if ($this->middleware === 'api') {
                    return true;
                }
                return false;
            case 'capture':
                return $this->sCapture($args[0]);
        }
    }
}
