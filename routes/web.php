<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', [UserController::class, 'dashboard']);
Route::get('/create', [UserController::class, 'create']);
Route::post('/insert', [UserController::class, 'insert']);
Route::get('/update/{id}', [UserController::class, 'updateView']);
Route::put('/updated/{id}', [UserController::class, 'update']);
