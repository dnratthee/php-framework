<?php

namespace App\Libs;

use App\Libs\Routing\Router;
use App\Libs\Routing\Route;
use App\Libs\Helper\Arr;

class Application extends Container
{

    public function __construct()
    {
        $this->register();
        Config::boot();
    }

    public static function config()
    {
        // TODO :: Config App
        return new static();
    }

    public function withRouting(
        ?string $web = null,
        ?string $api = null,
    ) {
        if (is_string($api) && realpath($api) !== false) {
            Route::middleware('api')->prefix('/api')->group($api);
        }

        if (is_string($web) && realpath($web) !== false) {
            Route::middleware('web')->group($web);
        }
        return $this;
    }

    public function handleRequest()
    {
        return Request::capture(Router::getRoutes());
    }

    public function run()
    {
        return $this;
    }
}
