<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', [UserController::class, 'dashboard']);
Route::get('/create', [UserController::class, 'create']);
