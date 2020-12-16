<?php

use App\Http\Controllers\ParamsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Authentication Routes
require_once __DIR__ . "/Auth/auth.php";

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'users'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/create', [UserController::class, 'store']);
    Route::put('/update/{id}', [UserController::class, 'update']);
    Route::delete('/delete/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix' => 'projects'], function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/{id}', [ProjectController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [ProjectController::class, 'store']);
    Route::put('/update/{id}', [ProjectController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->where(['id' => '[0-9]+']);
});

Route::group(['prefix' => 'tasks'], function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::get('/{id}', [TaskController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [TaskController::class, 'store']);
    Route::put('/update/{id}', [TaskController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->where(['id' => '[0-9]+']);
});

Route::group(['prefix' => 'server'], function () {
    Route::get('/', [ServerController::class, 'index']);
    Route::get('/{id}', [ServerController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [ServerController::class, 'store']);
    Route::put('/update/{id}', [ServerController::class, 'update'])->where(['id' => '[0-9]+']);
    Route::delete('/delete/{id}', [ServerController::class, 'destroy'])->where(['id' => '[0-9]+']);
});

Route::group(['prefix' => 'params'], function () {
    Route::get('/roles', [ParamsController::class, 'getAllRoles']);
});
