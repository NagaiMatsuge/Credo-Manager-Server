<?php

use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'server', 'middleware' => 'auth:api'], function () {
    Route::get('/', [ServerController::class, 'index']);
    Route::get('/{id}', [ServerController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [ServerController::class, 'store']);
    Route::put('/update/{id}', [ServerController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [ServerController::class, 'destroy'])->where(['id' => '[0-9]+']);
});
