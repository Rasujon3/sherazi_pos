<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;

Route::middleware('throttle:100,1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/dashboard', [ProductController::class, 'dashboard']);
    Route::get('/products/sales-report', [ProductController::class, 'salesReport']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/filter', [OrderController::class, 'filterByStatus']);
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/products', [ProductController::class, 'store']);
});
