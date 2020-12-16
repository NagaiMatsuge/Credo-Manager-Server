<?php

use App\Http\Controllers\ParamsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'params'], function () {
    Route::get('/roles', [ParamsController::class, 'getAllRoles']);
});