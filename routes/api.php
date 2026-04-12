<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\HospitalDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Test route
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// AI Chat route
Route::post('/chat', [\App\Http\Controllers\AIController::class, 'chat']);

// Auth routes
Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/login', [AuthController::class, 'login'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/resend-otp', [AuthController::class, 'resendOTP'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Hospital Dashboard routes
    Route::prefix('hospital')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\HospitalDashboardController::class, 'index']);
        Route::get('/profile', [\App\Http\Controllers\HospitalDashboardController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\HospitalDashboardController::class, 'updateProfile']);
        Route::post('/blood-request', [\App\Http\Controllers\HospitalDashboardController::class, 'storeBloodRequest']);
    });

    // Super Admin routes
    Route::prefix('superadmin')->group(function () {
        Route::get('/pending', [\App\Http\Controllers\SuperAdminController::class, 'pendingAdmins']);
        Route::post('/approve/{id}', [\App\Http\Controllers\SuperAdminController::class, 'approveAdmin']);
        Route::post('/reject/{id}', [\App\Http\Controllers\SuperAdminController::class, 'rejectAdmin']);
    });
});

// Dummy CRUD operations for items using UsersController
Route::get('/items', [UsersController::class, 'index']);
Route::get('/items/{id}', [UsersController::class, 'show']);
Route::post('/items', [UsersController::class, 'store']);
Route::put('/items/{id}', [UsersController::class, 'update']);
Route::patch('/items/{id}', [UsersController::class, 'patch']);
Route::delete('/items/{id}', [UsersController::class, 'destroy']);