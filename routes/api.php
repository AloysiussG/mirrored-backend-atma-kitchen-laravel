<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'loginByEmail']);

// --- PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-by-token', [AuthController::class, 'getUserDataByToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ...etc
});
