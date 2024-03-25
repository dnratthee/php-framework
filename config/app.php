<?php

return [
    'name' => env('APP_NAME', 'DNRatthee'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', 'false'),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'app_dir' => env('APP_DIR', __DIR__ . '/../') ?? __DIR__ . '/../',
];
