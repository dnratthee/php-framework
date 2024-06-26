<?php
require __DIR__ . '/../App/Libs/Helper/autoload.php';

use App\Libs\Application;

$app = Application::config()->withRouting(
    web: __DIR__ . '/../App/Routes/web.php',
    api: __DIR__ . '/../App/Routes/api.php'
)->run()->handleRequest();
