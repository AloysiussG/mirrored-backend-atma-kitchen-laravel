<?php

use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTES
Route::post('/login', [LoginController::class, 'loginByEmail']);

// --- PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginController::class, 'index']);
    Route::post('/logout', [LoginController::class, 'logout']);

    // ...etc
});
