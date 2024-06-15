<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Customer;
use App\Models\Hampers;
use App\Models\Karyawan;
use App\Models\PengadaanBahanBaku;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\PenggunaanBahanBaku;
use App\Models\Penitip;
use App\Models\Produk;
use App\Models\Resep;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Throwable;

class MainDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function mainDashAdmin()
    {
        try {
            $arr = [];
            $arr[] = ['name' => 'Produk', 'count' => Produk::count()];
            $arr[] = ['name' => 'Hampers', 'count' => Hampers::count()];
            $arr[] = ['name' => 'Resep', 'count' => Resep::count()];
            $arr[] = ['name' => 'Transaksi', 'count' => Transaksi::count()];
            $arr[] = ['name' => 'Bahan Baku', 'count' => BahanBaku::count()];
            $arr[] = ['name' => 'Customer', 'count' => Customer::count()];

            return response()->json(
                [
                    'data' => $arr,
                    'message' => 'Berhasil mengambil data.'
                ],
                200
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function mainDashMO()
    {
        try {
            $arr = [];
            $arr[] = ['name' => 'Karyawan', 'count' => Karyawan::count()];
            $arr[] = ['name' => 'Pengeluaran', 'count' => Pengeluaran::count()];
            $arr[] = ['name' => 'Penitip', 'count' => Penitip::count()];
            $arr[] = ['name' => 'Pengadaan Bahan Baku', 'count' => PengadaanBahanBaku::count()];
            $arr[] = ['name' => 'Penggunaan Bahan Baku', 'count' => PenggunaanBahanBaku::count()];

            return response()->json(
                [
                    'data' => $arr,
                    'message' => 'Berhasil mengambil data.'
                ],
                200
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function mainDashOwner()
    {
        try {
            $arr = [];
            $arr[] = ['name' => 'Karyawan', 'count' => Karyawan::count()];
            $arr[] = ['name' => 'Penggajian', 'count' => Penggajian::count()];

            return response()->json(
                [
                    'data' => $arr,
                    'message' => 'Berhasil mengambil data.'
                ],
                200
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
