<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'notifications', 'middleware' => 'auth:api'], function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/{id}', [NotificationController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [NotificationController::class, 'store']);
    Route::put('/update/{id}', [NotificationController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [NotificationController::class, 'destroy'])->where(['id' => '[0-9]+']);
});
