<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', [MessageController::class, 'index']);
    Route::get('/{message}', [MessageController::class, 'show'])->where(['message' => '[0-9]+']);
    Route::post('/create', [MessageController::class, 'store']);
    Route::put('/update/{message}', [MessageController::class, 'update'])->where(['message' => '[0-9]+']);
    Route::delete('/delete/{message}', [MessageController::class, 'destroy'])->where(['message' => '[0-9]+']);
});
