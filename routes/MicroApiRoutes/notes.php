<?php

use App\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'notes', 'middleware' => 'auth:api'], function () {
    Route::get('/', [NotesController::class, 'index']);
    Route::get('/{id}', [NotesController::class, 'show'])->where(['id' => '[0-9]+']);
    Route::post('/create', [NotesController::class, 'store']);
    Route::delete('/delete/{id}', [NotesController::class, 'index'])->where(['id' => '[0-9]+']);
 });