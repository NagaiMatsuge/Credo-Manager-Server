<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tasks'], function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::get('/{id}', [TaskController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [TaskController::class, 'store']);
    Route::put('/update/{id}', [TaskController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->where(['id' => '[0-9]+']);
});
