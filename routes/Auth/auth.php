<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Auth'], function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/password/forgot', [PasswordResetController::class, 'forgot']);
    Route::post('/password/reset', [PasswordResetController::class, 'reset']);
    Route::get('/create', [LoginController::class, 'create']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [LogoutController::class, 'logout']);
    });
});
