<?php

use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'steps', 'middleware' => 'auth:api'], function () {
    Route::get('/', [StepController::class, 'index']);
    Route::get('/{id}', [StepController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [StepController::class, 'store']);
    Route::put('/update/{id}', [StepController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [StepController::class, 'destroy'])->where(['id' => '[0-9]+']);
});
