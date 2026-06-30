<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MailAccountController;
use App\Http\Controllers\AdminController;

// Auth Routes moved to web.php

// Protected Routes
Route::middleware('auth:web')->group(function () {
    // Current User
    Route::get('/user', [AuthController::class, 'user']);

    // Internal Chat System
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{id}/messages', [ConversationController::class, 'storeMessage']);

    // External Mail System
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::patch('/messages/{id}', [MessageController::class, 'update']);

    // Employees Directory (Autocomplete)
    Route::get('/employees', [EmployeeController::class, 'index']);

    // Mail Account configs & syncing
    Route::get('/mail-accounts', [MailAccountController::class, 'index']);
    Route::post('/mail-accounts', [MailAccountController::class, 'store']);
    Route::post('/mail-accounts/{id}/sync', [MailAccountController::class, 'sync']);

    // Admin Panel Actions
    Route::middleware('can:admin-actions')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::post('/admin/users', [AdminController::class, 'storeUser']);
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/admin/conversations', [AdminController::class, 'conversations']);
    });
});
