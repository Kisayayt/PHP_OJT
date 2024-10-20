<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', [UserController::class, 'dashboard']);
Route::get('/create', [UserController::class, 'create']);
Route::post('/insert', [UserController::class, 'insert']);

Route::get('/update/{id}', [UserController::class, 'updateView'])->name('users.updateView');
Route::put('/updated/{id}', [UserController::class, 'update']);

Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
