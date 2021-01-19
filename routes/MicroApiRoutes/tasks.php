<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tasks'], function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::get('/{id}', [TaskController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [TaskController::class, 'store']);
    Route::put('/update/{id}', [TaskController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->where(['id' => '[0-9]+']);
    Route::get('/{id}/messages', [MessageController::class, 'getMessagesForTask'])->where(['id' => '[0-9]+']);
    Route::get('/data', [TaskController::class, 'getCredentials']);
    Route::get('/users', [TaskController::class, 'getUserListForCreatingTask']);
    Route::put('/clock', [TaskController::class, 'clock']);
});
