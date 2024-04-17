<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DetailHampersController;
use App\Http\Controllers\Api\HampersController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\PengadaanBahanBakuController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordChangeController;

// --- PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'loginByEmail']);
 // --- PASSWORD CHANGE
 Route::get('/password-change/verify/{verifyID}', [PasswordChangeController::class, 'verify']);

// khusus route GET /produk bisa search juga menggunakan URL query parameter
// contoh: /produk?search=milk&kategori=titipan
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk-by-kategori', [ProdukController::class, 'indexByKategoriProduk']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

// route GET /hampers juga bisa search juga menggunakan URL query parameter
Route::get('/hampers', [HampersController::class, 'index']);
Route::get('/hampers/{id}', [HampersController::class, 'show']);

// route GET /detail-hampers juga bisa search juga menggunakan URL query parameter
Route::get('/detail-hampers-by-hampers/{hampersId}', [DetailHampersController::class, 'indexByHampers']);
Route::get('/detail-hampers/{id}', [DetailHampersController::class, 'show']);



// --- PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    // --- AUTH/LOGIN USER
    Route::get('/user-by-token', [AuthController::class, 'getUserDataByToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //Karyawans
    Route::post('/karyawan', [KaryawanController::class, 'store']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'changePassword']);
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
    Route::get('/karyawan', [KaryawanController::class, 'index']);

    //Presensis
    Route::get('/presensi', [KaryawanController::class, 'index']);
    Route::post('/presensi', [KaryawanController::class, 'store']);
    Route::put('/presensi/{id}', [KaryawanController::class, 'update']);
    Route::get('/presensi/{id}', [KaryawanController::class, 'show']);

     // --- PASSWORD CHANGE
    Route::post('/password-change', [PasswordChangeController::class, 'store']);

    Route::post('/produk', [ProdukController::class, 'store']);
    Route::put('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);

    Route::post('/hampers', [HampersController::class, 'store']);
    Route::put('/hampers/{id}', [HampersController::class, 'updateAll']);
    Route::delete('/hampers/{id}', [HampersController::class, 'destroy']);

    Route::post('/detail-hampers/store/{hampersId}', [DetailHampersController::class, 'store']);
    Route::put('/detail-hampers/{id}', [DetailHampersController::class, 'update']);
    Route::delete('/detail-hampers/{id}', [DetailHampersController::class, 'destroy']);

    // route GET /pengadaan-bahan-baku juga bisa search juga menggunakan URL query parameter
    Route::get('/pengadaan-bahan-baku', [PengadaanBahanBakuController::class, 'index']);
    Route::post('/pengadaan-bahan-baku', [PengadaanBahanBakuController::class, 'store']);
    Route::get('/pengadaan-bahan-baku/{id}', [PengadaanBahanBakuController::class, 'show']);
    Route::put('/pengadaan-bahan-baku/{id}', [PengadaanBahanBakuController::class, 'update']);
    Route::delete('/pengadaan-bahan-baku/{id}', [PengadaanBahanBakuController::class, 'destroy']);


    // ...etc
});
