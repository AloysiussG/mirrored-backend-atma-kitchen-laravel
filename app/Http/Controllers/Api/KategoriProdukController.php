<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Throwable;

class KategoriProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $kategoriProduk = KategoriProduk::query()
                ->withCount('produks');

            if ($request->hasProduk && $request->hasProduk == 'true') {
                $kategoriProduk->having('produks_count', '>', 0);
            }

            $kategoriProdukData = $kategoriProduk->get();

            return response()->json(
                [
                    'data' => $kategoriProdukData,
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
