<?php

use Illuminate\Support\Facades\Route;
use App\Models\Role;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\AuthController;

// 👉 Anyone can access these routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

// 👉 Nilai endpoints
Route::get('nilaiRT', [NilaiController::class, 'nilaiRT'])->name('nilai.rt');
Route::get('nilaiST', [NilaiController::class, 'nilaiST'])->name('nilai.st');

// 👉 Only authenticated users can access these routes
Route::group([
    'middleware' => 'authenticated',
], function () {
    // 👉 Auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
    });

    // 👉 Masterdata
    Route::get('roles/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);

    Route::apiResource('divisions', DivisionController::class);
    Route::apiResource('employees', EmployeeController::class);

    // // 👉 Nilai endpoints
    // Route::get('nilaiRT', [NilaiController::class, 'nilaiRT'])->name('nilai.rt');
    // Route::get('nilaiST', [NilaiController::class, 'nilaiST'])->name('nilai.st');
});
