<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy'])->where(['id' => '[0-9]+git']);

});

Route::group(['prefix' => 'projects'], function () {
    
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{id}', [ProjectController::class, 'show']);
    Route::post('projects', [ProjectController::class, 'store']);
    Route::put('projects/{id}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->where(['id' => '[0-9]+git']);

});

Route::group(['prefix' => 'tasks'], function () {

    Route::get('task', [TaskController::class, 'index']);
    Route::get('task', [TaskController::class, 'show']);
    Route::post('task', [TaskController::class, 'store']);
    Route::put('task/{id}', [TaskController::class, 'update']);
    Route::delete('task/{task}', [TaskController::class, 'destroy'])->where(['id' => '[0-9]+git']);
    
});
