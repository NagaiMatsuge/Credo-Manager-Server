<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'main', 'middleware' => 'auth:api'], function () {
    Route::get('/notifications', [NotificationController::class, "index"]);
    Route::get('/projects', [MainController::class, 'showProjectsToAdmin']);
    Route::get('/users', [MainController::class, 'showUsersToAdmin']);
    Route::get('/unreadMessages', [MainController::class, 'showUnreadMessages']);
});
