<?php

namespace App\Libs;

use App\Libs\Helper\Arr;

class Request
{
    protected $params = [];
    protected $json = [];
    protected $body = [];

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
            $this->params[$key] = $value;
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

    public function getBody()
    {
        return $this->body;
    }

    public function __get($key)
    {
        return $this->params[$key] ?? $this->body[$key] ?? null;
    }

    public static function capture(\App\Libs\Routing\RouteContainer $container)
    {
        $router = $container->getRoutes();
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method =  $_SERVER['REQUEST_METHOD'];

        try {
            if (!array_key_exists($method, $router)) {
                http_response_code(405);
                echo "Method not allowed";
                exit;
            }

            $routes = $router[$method];

            if (array_key_exists($uri, $routes)) {
                $controller = $router[$method][$uri]['controller'];
                $action = $router[$method][$uri]['action'];
                $controller = new $controller();
                $controller->$action(new Request());
            } else if (Arr::preg_array_key_exists($uri, $routes)[0]) {
                $path = Arr::preg_array_key_exists($uri, $routes)[1];
                if (!$router[$method][$path]['param']) {
                    http_response_code(404);
                    echo "404 Not Found";
                    return;
                }

                $data = [];
                foreach ($router[$method][$path]['param'] as $k => $p) {
                    $data[$p] = explode("/", $uri)[$k];
                }

                $controller = $router[$method][$path]['controller'];
                $action = $router[$method][$path]['action'];
                $controller = new $controller();
                $controller->$action(new Request(...$data));
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
}
