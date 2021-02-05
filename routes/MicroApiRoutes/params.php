<?php

use App\Http\Controllers\ParamsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'params', 'middleware' => 'auth:api'], function () {
    Route::get('/roles/{id}', [ParamsController::class, 'getAllRoles']);
});
