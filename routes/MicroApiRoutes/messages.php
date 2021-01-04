<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'messages'], function () {
    Route::post('/create', [MessageController::class, 'store']);
    Route::delete('/delete/{message}', [MessageController::class, 'destroy'])->where(['message' => '[0-9]+']);
});
