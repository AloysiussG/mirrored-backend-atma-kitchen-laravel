<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordChangeController;

// --- PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'loginByEmail']);
 // --- PASSWORD CHANGE
 Route::get('/password-change/verify/{verifyID}', [PasswordChangeController::class, 'verify']);

// --- PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    // --- AUTH/LOGIN USER
    Route::get('/user-by-token', [AuthController::class, 'getUserDataByToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

     // --- PASSWORD CHANGE
    Route::post('/password-change', [PasswordChangeController::class, 'store']);

    // ...etc
});
