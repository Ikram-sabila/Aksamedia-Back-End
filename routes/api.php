<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{KaryawanController, DivisiController, AuthController};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated user routes
    Route::prefix('user')->group(function () {
        Route::put('/update', [AuthController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Employee routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [KaryawanController::class, 'index']);
        Route::post('/', [KaryawanController::class, 'store']);
        Route::get('/{id}', [KaryawanController::class, 'show']);
        Route::put('/{id}', [KaryawanController::class, 'update']);
        Route::delete('/{id}', [KaryawanController::class, 'destroy']);
    });

    // Division routes
    Route::prefix('divisions')->group(function () {
        Route::get('/', [DivisiController::class, 'index']);
    });
});