<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HampersController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'loginByEmail']);

// khusus route GET /produk bisa search juga menggunakan URL query parameter
// contoh: /produk?search=milk&kategori=titipan
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk-by-kategori', [ProdukController::class, 'indexByKategoriProduk']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

// route GET /hampers juga bisa search juga menggunakan URL query parameter
Route::get('/hampers', [HampersController::class, 'index']);
Route::get('/hampers/{id}', [HampersController::class, 'show']);


// --- PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-by-token', [AuthController::class, 'getUserDataByToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/produk', [ProdukController::class, 'store']);
    Route::put('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);

    Route::post('/hampers', [HampersController::class, 'store']);
    Route::put('/hampers/{id}', [HampersController::class, 'updateAll']);
    Route::delete('/hampers/{id}', [HampersController::class, 'destroy']);

    // ...etc
});
