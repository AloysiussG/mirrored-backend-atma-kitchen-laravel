<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DetailHampersController;
use App\Http\Controllers\Api\DetailResepController;
use App\Http\Controllers\Api\HampersController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\PengadaanBahanBakuController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordChangeController;
use App\Http\Controllers\Api\PenitipController;
use App\Http\Controllers\Api\PengeluaranController;
use App\Http\Controllers\Api\BahanBakuController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\KategoriProdukController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\PenggajianController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\ResepController;


// --- PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'loginByEmail']);

// password change
Route::get('/password-change/verify/{verifyID}', [PasswordChangeController::class, 'verify']);

// khusus route GET /produk bisa search juga menggunakan URL query parameter
// contoh: /produk?search=milk&kategori=titipan
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk-by-kategori', [ProdukController::class, 'indexByKategoriProduk']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

Route::get('/kategori', [KategoriProdukController::class, 'index']);

// route GET /hampers juga bisa search juga menggunakan URL query parameter
Route::get('/hampers', [HampersController::class, 'index']);
Route::get('/hampers/{id}', [HampersController::class, 'show']);

// route GET /detail-hampers juga bisa search juga menggunakan URL query parameter
Route::get('/detail-hampers-by-hampers/{hampersId}', [DetailHampersController::class, 'indexByHampers']);
Route::get('/detail-hampers/{id}', [DetailHampersController::class, 'show']);

// register customer
Route::post('/my-customer/', [CustomerController::class, 'store']);




// --- PROTECTED ROUTES

// ability vs abilities, misal: [customer, admin] 
// ability === at least 1 ability terpenuhi, grant access
// abilities === semua abilities dalam array harus terpenuhi baru bisa grant access

// --- --- ALL ABILITIES, SEMUA TOKEN BISA AKSES ['*'] 
Route::middleware(['auth:sanctum'])->group(function () {
    // auth/login user
    Route::get('/user-by-token', [AuthController::class, 'getUserDataByToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// --- --- OWNER + MANAGER OPERASIONAL
Route::middleware(['auth:sanctum', 'ability:owner,mo'])->group(function () {
    // nanti route API untuk laporan yang bisa dilihat Owner + MO ditaruh disini
});

// --- --- CUSTOMER ONLY
Route::middleware(['auth:sanctum', 'ability:customer'])->group(function () {
    // Customers
    Route::post('/my-customer/update', [CustomerController::class, 'update']);
    Route::get('/my-customer', [CustomerController::class, 'show']);
    Route::get('/my-customer/showHistory', [CustomerController::class, 'showHistory']);
    Route::post('/my-customer/searchHistory', [CustomerController::class, 'searchHistory']);

    // --- PASSWORD CHANGE CUSTOMER
    Route::post('/password-change', [PasswordChangeController::class, 'store']);
});

// --- --- OWNER ONLY
Route::middleware(['auth:sanctum', 'ability:owner'])->group(function () {
    // ubah data gaji dan bonus karyawan
    Route::put('/karyawan/changeGaji/{id}', [KaryawanController::class, 'changeGaji']);

    // ... nanti route API untuk laporan yang bisa dilihat Owner ditaruh juga disini
});

// --- --- ADMIN ONLY
Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
    // Admin
    Route::post('/produk', [ProdukController::class, 'store']);
    // bukan PUT, karena PHP cuma bisa nerima request file di POST request
    // kalo PUT gabisa update foto nanti...
    Route::post('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);

    Route::post('/hampers', [HampersController::class, 'store']);
    Route::put('/hampers/{id}', [HampersController::class, 'updateAll']);
    Route::delete('/hampers/{id}', [HampersController::class, 'destroy']);

    Route::post('/detail-hampers/store/{hampersId}', [DetailHampersController::class, 'store']);
    Route::put('/detail-hampers/{id}', [DetailHampersController::class, 'update']);
    Route::delete('/detail-hampers/{id}', [DetailHampersController::class, 'destroy']);

    // Reseps
    Route::post('/resep', [ResepController::class, 'store']);
    Route::get('/resep', [ResepController::class, 'index']);
    Route::get('/resep/{id}', [ResepController::class, 'show']);
    Route::delete('/resep/{id}', [ResepController::class, 'destroy']);
    Route::put('/resep/{id}', [ResepController::class, 'update']);

    // DetailReseps
    Route::post('/detail-resep', [DetailResepController::class, 'store']);
    Route::get('/detail-resep/{id}', [DetailResepController::class, 'show']);
    Route::put('/detail-resep/{id}', [DetailResepController::class, 'update']);
    Route::delete('/detail-resep/{id}', [DetailResepController::class, 'destroy']);

    // route GET /bahanBaku juga bisa search juga menggunakan URL query parameter
    Route::get('/bahan-baku', [BahanBakuController::class, 'index']);
    Route::get('/bahan-baku/{id}', [BahanBakuController::class, 'show']);
    Route::post('/bahan-baku', [BahanBakuController::class, 'store']);
    Route::put('/bahan-baku/{id}', [BahanBakuController::class, 'update']);
    Route::delete('/bahan-baku/{id}', [BahanBakuController::class, 'destroy']);

    //Lihat customer by admin
    Route::get('/customer', [CustomerController::class, 'index']);

    //liat history & show transaksi di admin
    Route::get('/findTransaksiByCust', [TransaksiController::class, 'findByCustomer']);
    Route::get('/transaksiProducts/{id}', [TransaksiController::class, 'showWithProducts']);
});

// --- --- MANAGER OPERASIONAL ONLY
Route::middleware(['auth:sanctum', 'ability:manager-operasional'])->group(function () {
    // MO
    Route::get('/pengadaan-bahan-baku', [PengadaanBahanBakuController::class, 'index']);
    Route::post('/pengadaan-bahan-baku', [PengadaanBahanBakuController::class, 'store']);
    Route::get('/pengadaan-bahan-baku/{id}', [PengadaanBahanBakuController::class, 'show']);
    Route::put('/pengadaan-bahan-baku/{id}', [PengadaanBahanBakuController::class, 'update']);
    Route::delete('/pengadaan-bahan-baku/{id}', [PengadaanBahanBakuController::class, 'destroy']);

    // Karyawans
    Route::post('/karyawan', [KaryawanController::class, 'store']);
    Route::post('/karyawan/changePassword', [KaryawanController::class, 'changePassword']);
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
    Route::get('/karyawan', [KaryawanController::class, 'index']);

    // Presensis
    Route::get('/presensi', [PresensiController::class, 'index']);
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::put('/presensi/{id}', [PresensiController::class, 'update']);
    Route::get('/presensi/{id}', [PresensiController::class, 'show']);

    // Penggajians
    Route::get('/penggajian', [PenggajianController::class, 'index']);
    Route::post('/penggajian', [PenggajianController::class, 'store']);
    Route::get('/penggajian/{id}', [PenggajianController::class, 'show']);
    Route::put('/penggajian/{id}', [PenggajianController::class, 'update']);
    Route::delete('/penggajian/{id}', [PenggajianController::class, 'destroy']);

    // route GET /penitip juga bisa search juga menggunakan URL query parameter
    Route::get('/penitip', [PenitipController::class, 'index']);
    Route::get('/penitip/{id}', [PenitipController::class, 'show']);
    Route::post('/penitip', [PenitipController::class, 'store']);
    Route::put('/penitip/{id}', [PenitipController::class, 'update']);
    Route::delete('/penitip/{id}', [PenitipController::class, 'destroy']);

    // route GET /pengeluaran juga bisa search juga menggunakan URL query parameter
    Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
    Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show']);
    Route::post('/pengeluaran', [PengeluaranController::class, 'store']);
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update']);
    Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy']);
});
