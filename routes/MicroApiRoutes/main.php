<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'main', 'middleware' => 'auth:api'], function () {
    Route::get('/mid', [MainController::class, 'mid']);
    Route::get('/last', [MainController::class, 'last']);
});
