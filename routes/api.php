<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 1. المسارات العامة
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);

// 2. المسارات المحمية بـ Sanctum (لكل المستخدمين)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('CheckUser:Admin')->group(function () {
        Route::apiResource('users', UserController::class)->only(['index', 'show']);
        Route::patch('users/{id}/status', [UserController::class, 'changeStatus']);
    });

    Route::middleware('IsOwnerOrAdmin')->group(function () {
    Route::apiResource('users', UserController::class)->only(['update', 'destroy']);
});

    // تسجيل الخروج
    Route::post('logout', [AuthController::class, 'logout']);
});