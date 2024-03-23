<?php

namespace App\Route;

use App\Controllers\UserController;
use App\Controllers\RoomController;
use App\Libs\Routing\Route;

Route::controller(RoomController::class)->prefix('/room')->group(function () {
    Route::get('', 'getAll');
    Route::get('/{id}', 'getOne');

    Route::post('', 'store');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'delete');

    Route::get('/temp1/{temp1}', 'temp1');
});

Route::controller(UserController::class)->prefix('/user')->group(function () {
    Route::get('', 'getAll');
    Route::get('/{id}', 'getOne');

    Route::post('/auth', 'auth');
});
