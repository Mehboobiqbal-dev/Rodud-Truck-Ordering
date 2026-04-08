<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AdminMessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (no auth required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Messages
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/unread-count', [MessageController::class, 'unreadCount']);
        Route::post('/{message}/mark-read', [MessageController::class, 'markAsRead']);
        Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead']);
    });

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Support
    Route::post('/support', [\App\Http\Controllers\Api\SupportController::class, 'store']);

    // Admin Messages (protected by admin middleware)
    Route::middleware('admin')->prefix('admin/messages')->group(function () {
        Route::post('/send', [AdminMessageController::class, 'sendToUser']);
        Route::get('/user/{user}', [AdminMessageController::class, 'getUserMessages']);
        Route::get('/support', [AdminMessageController::class, 'getSupportMessages']);
    });
});

