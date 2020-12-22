<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'projects'], function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/{id}', [ProjectController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [ProjectController::class, 'store']);
    Route::put('/update/{id}', [ProjectController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->where(['id' => '[0-9]+']);
    Route::get('/create', [ProjectController::class, 'getCredentials']);
    Route::get('/{project}/steps', [ProjectController::class, 'getProjectSteps'])->where(['id' => '[0-9]+']);

});
