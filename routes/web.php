<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// User management routes
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
Route::get('/create', [UserController::class, 'create']);
Route::post('/insert', [UserController::class, 'insert']);

Route::get('/update/{id}', [UserController::class, 'updateView'])->name('users.updateView');
Route::put('/updated/{id}', [UserController::class, 'update']);

Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');


// Departments management routes
Route::get('/departmentDashboard', [DepartmentController::class, 'departmentDashboard'])->name("departments.index");
Route::get('/createDepartment', [DepartmentController::class, 'insertDepartmentView'])->name("departments.createView");
Route::post('/insertDepartment', [DepartmentController::class, 'insertDepartment'])->name("departments.insertDepartment");

Route::get('/updateDepartment/{id}', [DepartmentController::class, 'updateDepartmentView'])->name("departments.updateView");
Route::put('/updatedDepartment/{id}', [DepartmentController::class, 'updateDepartment']);

Route::delete('/deleteDepartment/{id}', [DepartmentController::class, 'deleteDepartment']);
Route::delete('/departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('departments.bulkDelete');

Route::get('/departmentDashboard/{id}/details', [DepartmentController::class, 'details'])->name('departments.details');

Route::get('/departments/{id}/update-status', [DepartmentController::class, 'updateStatus'])->name('departments.updateStatus');
