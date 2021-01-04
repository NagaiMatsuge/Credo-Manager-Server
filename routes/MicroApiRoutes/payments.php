<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'payments', 'middleware' => 'auth:api'], function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::get('/{payment}', [PaymentController::class, 'show'])->where(['payment' => '[0-9]+']);
    Route::post('/create', [PaymentController::class, 'store']);
    Route::put('/update/{payment}', [PaymentController::class, 'update'])->where(['payment' => '[0-9]+']);
    Route::delete('/delete/{payment}', [PaymentController::class, 'destroy'])->where(['payment' => '[0-9]+']);
});
