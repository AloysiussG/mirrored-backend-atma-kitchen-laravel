<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Throwable;

class KategoriProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $kategoriProduk = KategoriProduk::query()
                ->withCount('produks')
                ->having('produks_count', '>', 0)
                ->get();

            return response()->json(
                [
                    'data' => $kategoriProduk,
                    'message' => 'Berhasil mengambil data kategori produk.'
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
}
