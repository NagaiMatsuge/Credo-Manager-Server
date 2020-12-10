<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Auth'], function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LogoutController::class, 'logout']);
});
