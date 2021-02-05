<?php

use App\Http\Controllers\Auth\EmailVerifyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Auth'], function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/password/forgot', [PasswordResetController::class, 'forgot']);
    Route::post('/password/reset', [PasswordResetController::class, 'reset']);
    Route::get('/create', [LoginController::class, 'create']);
    Route::get('/verify/email/{id}', [EmailVerifyController::class, 'verify'])->name('auth.email.verify');
    // Route::post('/verify/email', [EmailVerifyController::class, 'verifyAgain']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [LogoutController::class, 'logout']);
        Route::get('/user', [UserController::class, 'getUser']);
        Route::post('/register', [RegisterController::class, 'register']);
    });
});
