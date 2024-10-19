<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', [UserController::class, 'dashboard']);



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
