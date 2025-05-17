<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TerminationController;
use App\Http\Controllers\Api\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout']);

    // Add your protected API routes here
    // Example:
    // Route::apiResource('tasks', App\Http\Controllers\TaskController::class);
});

// API Health Check
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});

Route::post('/start-termination', [TerminationController::class, 'start']);

// Evolution API Webhook
Route::post('/webhook/evolution', [App\Http\Controllers\Api\WebhookController::class, 'handle']);

Route::get('/token/{token}', [TokenController::class, 'show']);
