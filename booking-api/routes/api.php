<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminResourceController;
use App\Http\Controllers\Admin\AdminBookingController;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;


// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/resources', [ResourceController::class, 'index']);
Route::get('/resources/{id}', [ResourceController::class, 'show']);
Route::get('/resources/{id}/availability', [ResourceController::class, 'availability']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return new \App\Http\Resources\UserResource($request->user());
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/bookings', BookingController::class);
    Route::post('/payments', [PaymentController::class, 'createIntent']);
    Route::post('/payments/confirm', [PaymentController::class, 'confirm']);

    // Routes Admin

    Route::middleware(['auth:sanctum', 'can:isAdmin'])->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);

        Route::apiResource('/resources', AdminResourceController::class);
        Route::apiResource('/bookings', AdminBookingController::class);
        Route::get('/users', [AdminController::class, 'users']);
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole']);
    });
});
