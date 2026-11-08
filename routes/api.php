<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StylistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\MeasurementProfileController;
use App\Http\Controllers\Api\StockItemController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Middleware\EnsureRole;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(EnsureRole::class.':stylist')->prefix('stylist')->group(function () {
        Route::get('/dashboard', [StylistController::class, 'dashboard']);
        Route::get('/analytics', [StylistController::class, 'analytics']);
        Route::apiResource('orders', OrderController::class)->except(['show']);
        Route::apiResource('clients', ClientController::class)->only(['index','store','show','update']);
        Route::apiResource('measurement-profiles', MeasurementProfileController::class);
        Route::apiResource('stock-items', StockItemController::class);
        Route::apiResource('appointments', AppointmentController::class);
        Route::apiResource('products', ProductController::class);
    });

    Route::middleware(EnsureRole::class.':client')->prefix('client')->group(function () {
        Route::get('/orders', [OrderController::class, 'clientIndex']);
        Route::post('/orders', [OrderController::class, 'clientStore']);
        Route::apiResource('measurement-profiles', MeasurementProfileController::class)->only(['index','store','show','update','destroy']);
    });

    Route::middleware(EnsureRole::class.':admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/stylists', [AdminController::class, 'stylists']);
        Route::get('/orders', [AdminController::class, 'orders']);
        Route::patch('/stylists/{stylist}', [AdminController::class, 'updateStylist']);
    });

    Route::get('/stylists', [CatalogController::class, 'index']);
    Route::get('/stylists/{stylist}', [CatalogController::class, 'show']);
});
