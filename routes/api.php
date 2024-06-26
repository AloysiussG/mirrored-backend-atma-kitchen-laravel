<?php

use App\Http\Controllers\Api\AlamatController;
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
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DetailCartController;
use App\Http\Controllers\Api\KategoriProdukController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\LaporanGreController;
use App\Http\Controllers\Api\LaporanSamController;
use App\Http\Controllers\Api\MainDashboardController;
use App\Http\Controllers\Api\PackagingController;
use App\Http\Controllers\Api\PemrosesanPesananController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\PenggajianController;
use App\Http\Controllers\Api\PermintaaanRefundController;
use App\Http\Controllers\Api\PenggunaanBahanBakuController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\ProdukUniqueController;
use App\Http\Controllers\Api\ResepController;
use App\Http\Controllers\Api\RoleController;

// --- PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'loginByEmail']);

//test nanti hapus pls janlup
Route::get('/test/{id}', [TransaksiController::class, 'testbahanBakuTransaksi']);

// password change
Route::get('/password-change/verify/{verifyID}', [PasswordChangeController::class, 'verify']);
Route::post('/forgotPassword', [PasswordChangeController::class, 'forgotPass']);

// khusus route GET /produk bisa search juga menggunakan URL query parameter
// contoh: /produk?search=milk&kategori=titipan
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk-by-kategori', [ProdukController::class, 'indexByKategoriProduk']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

// produk unique
Route::get('/produk-unique', [ProdukUniqueController::class, 'index']);

Route::get('/kategori', [KategoriProdukController::class, 'index']);

// route GET /hampers juga bisa search juga menggunakan URL query parameter
Route::get('/hampers', [HampersController::class, 'index']);
Route::get('/hampers/{id}', [HampersController::class, 'show']);

// route GET /detail-hampers juga bisa search juga menggunakan URL query parameter
Route::get('/detail-hampers-by-hampers/{hampersId}', [DetailHampersController::class, 'indexByHampers']);
Route::get('/detail-hampers/{id}', [DetailHampersController::class, 'show']);

// register customer
Route::post('/register', [CustomerController::class, 'store']);

//role
Route::get('/role', [RoleController::class, 'index']);


// sementara buat cek
// Route::get('/generateNomorNota', [CartController::class, 'generateNomorNota']);



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
Route::middleware(['auth:sanctum', 'ability:owner,manager-operasional'])->group(function () {
    // nanti route API untuk laporan yang bisa dilihat Owner + MO ditaruh disini
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);

    // laporan
    Route::get('/laporanPendapatan/{tahun}', [LaporanGreController::class, 'laporanPendapatan']);
    Route::post('/laporanBahanBaku', [LaporanGreController::class, 'laporanBahanBaku']);
    Route::get('/testsam', [LaporanSamController::class, 'laporan_presensi']);
    Route::get('/testsam2', [LaporanSamController::class, 'laporan_penitip']);
    Route::get('/testsam3', [LaporanSamController::class, 'laporanPengeluaranPemasukan']);

    // laporan penjualan bulanan per produk
    Route::post('/laporan-penjualan-bulanan-per-produk', [LaporanController::class, 'indexLaporanPenjualanBulananPerProduk']);

    // laporan stok bahan baku
    Route::get('/laporan-stok-bahan-baku', [LaporanController::class, 'indexLaporanStokBahanBaku']);
});

// --- --- ADMIN + MANAGER OPERASIONAL
Route::middleware(['auth:sanctum', 'ability:admin,manager-operasional'])->group(function () {
    // route GET /penitip juga bisa search juga menggunakan URL query parameter
    Route::get('/penitip', [PenitipController::class, 'index']);
    // route GET /bahanBaku juga bisa search juga menggunakan URL query parameter
    Route::get('/bahan-baku', [BahanBakuController::class, 'index']);
    Route::get('/packaging', [PackagingController::class, 'indexByItemId']);
});

// --- --- OWNER + MANAGER OPERASIONAL + ADMIN
Route::middleware(['auth:sanctum', 'ability:owner,manager-operasional,admin'])->group(function () {
    Route::post('/karyawan/changePassword', [KaryawanController::class, 'changePassword']);
});

// --- --- CUSTOMER ONLY
Route::middleware(['auth:sanctum', 'ability:customer'])->group(function () {
    // Customers
    Route::post('/my-customer/update', [CustomerController::class, 'update']);
    Route::get('/my-customer', [CustomerController::class, 'show']);
    Route::get('/my-customer/indexPesanan', [CustomerController::class, 'indexPesanan']);
    Route::get('/my-customer/indexPesananTerkirim', [CustomerController::class, 'indexPesananTerkirim']);
    Route::get('/my-customer/{id}', [CustomerController::class, 'showPesanan']);
    Route::put('/my-customer/updateToken', [CustomerController::class, 'updateToken']);


    // --- PASSWORD CHANGE CUSTOMER
    Route::post('/password-change', [PasswordChangeController::class, 'store']);

    // Konfirmasi Pesanan Selesai
    Route::put('/my-customer/updateStatusSelesai/{id}', [CustomerController::class, 'updateStatusSelesai']);


    // --- Customer pasang bukti transaaksi
    Route::post('/pasangbukti/{id}', [TransaksiController::class, 'updateBukti']);
    // CART
    Route::get('/my-cart', [CartController::class, 'index']);
    Route::get('/my-cart/show-nota/{id}', [CartController::class, 'showNota']);
    Route::post('/my-cart/cek-ketersediaan', [CartController::class, 'cekKetersediaanByTanggalAmbil']);
    Route::post('/my-cart/add-to-cart', [DetailCartController::class, 'addToCart']);
    Route::delete('/my-cart/remove-from-cart/{id}', [DetailCartController::class, 'removeFromCart']);
    Route::put('/my-cart/update-detail-cart-count/{id}', [DetailCartController::class, 'updateDetailCartCount']);
    Route::put('/my-cart/update-detail-cart-status/{id}', [DetailCartController::class, 'updateDetailCartStatus']);
    Route::post('/my-cart/confirm-order', [CartController::class, 'confirmOrder']);

    // alamat
    Route::get('/my-alamat', [AlamatController::class, 'indexMyAlamat']);

    //penarikan saldo
    Route::get('/penarikan-saldo', [PermintaaanRefundController::class, 'indexByCustomer']);
    Route::post('/penarikan-saldo', [PermintaaanRefundController::class, 'kirimRequest']);
});

// --- --- OWNER ONLY
Route::middleware(['auth:sanctum', 'ability:owner'])->group(function () {
    // ubah data gaji dan bonus karyawan
    Route::put('/changeGaji/{id}', [KaryawanController::class, 'changeGaji']);

    // main dash
    Route::get('/main-dash-owner', [MainDashboardController::class, 'mainDashOwner']);

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
    // bukan PUT, karena PHP cuma bisa nerima request file di POST request
    // kalo PUT gabisa update foto nanti...
    Route::post('/hampers/{id}', [HampersController::class, 'updateAll']);
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
    Route::get('/detail-resep/{id}', [DetailResepController::class, 'index']);
    Route::get('/detail-resep/show/{id}', [DetailResepController::class, 'show']);
    Route::put('/detail-resep/{id}', [DetailResepController::class, 'update']);
    Route::delete('/detail-resep/{id}', [DetailResepController::class, 'destroy']);

    Route::get('/bahan-baku/{id}', [BahanBakuController::class, 'show']);
    Route::post('/bahan-baku', [BahanBakuController::class, 'store']);
    Route::put('/bahan-baku/{id}', [BahanBakuController::class, 'update']);
    Route::delete('/bahan-baku/{id}', [BahanBakuController::class, 'destroy']);

    //Lihat customer by admin
    Route::get('/customer', [CustomerController::class, 'index']);

    //liat history & show transaksi di admin
    Route::get('/findTransaksiByCust', [TransaksiController::class, 'findByCustomer']);
    Route::get('/transaksiProducts/{id}', [TransaksiController::class, 'showWithProducts']);
    Route::put('/updateOngkir/{id}', [TransaksiController::class, 'updateOngkir']);
    Route::put('/updatePembayaran/{id}', [TransaksiController::class, 'updatePembayaran']);
    Route::get('/indexDiproses', [TransaksiController::class, 'indexDiproses']);
    Route::get('/indexTelatBayar', [TransaksiController::class, 'indexTelatBayar']);
    Route::get('/indexMenungguKonfirmasi', [TransaksiController::class, 'indexMenungguKonfirmasi']);

    //status pesanan
    Route::put('/updateStatusTransaksi/{id}', [TransaksiController::class, 'updateStatusTransaksi']);

    //permintaan refund
    Route::get('/permintaan-refund-admin', [PermintaaanRefundController::class, 'indexByStatus']);
    Route::put('/permintaan-refund-admin/{id}', [PermintaaanRefundController::class, 'terimaRequest']);

    // main dash
    Route::get('/main-dash-admin', [MainDashboardController::class, 'mainDashAdmin']);
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
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);

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

    //untuk ngambil transaksi yang dibutuhkan sama MO
    Route::get('/findTransaksis', [TransaksiController::class, 'findByCustomer']);
    Route::get('/transaksiProduct/{id}', [TransaksiController::class, 'showWithProducts']);

    //terima or tolak pesanan
    Route::put('/terimaPesanan/{id}', [TransaksiController::class, 'updateTerimaTolak']);

    //get bahan baku by the transaksi
    Route::get('/bahanWarning', [TransaksiController::class, 'bahanBakuTransaksi']);

    // PEMROSESAN PESANAN
    // list pesanan harian, yang perlu diproses hari ini (h-1 tanggal ambil)
    // 1. list pesanan harian ---> hanya untuk tampilan di web saja, list transaksi & produk & bahan baku yg dibutuhkan
    Route::get('/list-pesanan-harian', [PemrosesanPesananController::class, 'index']);

    Route::get('/list-pesanan-belum-diproses/{id}', [TransaksiController::class, 'warnBahanBaku']);
    // 2. list transaksi harian ---> untuk confirm proses/tidak
    // Route::get('/list-transaksi-harian', [PemrosesanPesananController::class, 'indexTransaksiPerluDiproses']);
    Route::get('/list-pesanan-harian/cek/{id}', [PemrosesanPesananController::class, 'cekProsesTransaksi']);
    Route::put('/list-pesanan-harian/proses/{id}', [PemrosesanPesananController::class, 'prosesTransaksi']);

    Route::get('/list-pesanan-harian/cek-semua', [PemrosesanPesananController::class, 'cekProsesSemuaTransaksi']);
    Route::put('/list-pesanan-harian/proses-semua', [PemrosesanPesananController::class, 'prosesSemuaTransaksi']);

    // penggunaan bahan baku
    Route::get('/penggunaan-bahan-baku', [PenggunaanBahanBakuController::class, 'index']);

    // main dash
    Route::get('/main-dash-mo', [MainDashboardController::class, 'mainDashMO']);
});
