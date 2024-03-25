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
});

Route::controller(UserController::class)->prefix('/user')->group(function () {
    Route::get('/signout', 'logOut');
    Route::post('/register', 'store');
    Route::post('/auth', 'auth');
});
