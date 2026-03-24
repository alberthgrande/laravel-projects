<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/test', [TestController::class, 'index']);

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class,'login']);

    // 🔐 Admin only
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::post('/admin/create', [AuthController::class, 'createAdmin']);
    });


    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Product CRUD using apiResource
        Route::apiResource('products', ProductController::class);
    });
});
