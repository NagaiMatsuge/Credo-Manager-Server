<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tasks', 'middleware' => 'auth:api'], function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::get('/{id}', [TaskController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [TaskController::class, 'store']);
    Route::put('/update/{task}', [TaskController::class, 'update'])->where(['task' => '[0-9]+']);
    Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->where(['id' => '[0-9]+']);
    Route::get('/{id}/messages', [TaskController::class, 'showMessages'])->where(['id' => '[0-9]+']);
    Route::get('/data', [TaskController::class, 'getCredentials']);
});
