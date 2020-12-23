<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'payments'], function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::get('/{id}', [PaymentController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [PaymentController::class, 'store']);
    Route::put('/update/{id}', [PaymentController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [PaymentController::class, 'destroy'])->where(['id' => '[0-9]+']);
});