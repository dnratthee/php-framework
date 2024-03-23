<?php

use App\Controllers\HomeController;
use App\Controllers\RoomController;
use App\Libs\Routing\Route;


Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index');
});

Route::get('/room', [RoomController::class, 'index']);
