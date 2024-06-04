<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukUnique;
use Illuminate\Http\Request;
use Throwable;

class ProdukUniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $produksQuery = ProdukUnique::query()->with('produks.kategoriProduk', 'produks.penitip', 'produks.packagings.bahanBaku');

            // // FILTER METHOD #1
            // if ($request->status) {
            //     $produksQuery->where('status', 'like', '%' . $request->status . '%');
            // }

            // if ($request->kategori) {
            //     $produksQuery->whereHas('kategoriProduk', function ($query) use ($request) {
            //         $query->where('nama_kategori_produk', 'like', '%' . $request->kategori . '%');
            //     });
            // }

            // if ($request->penitip) {
            //     $produksQuery->whereHas('penitip', function ($query) use ($request) {
            //         $query->where('nama_penitip', 'like', '%' . $request->penitip . '%');
            //     });
            // }

            // // FILTER METHOD #2
            // if ($request->kategoriProduk) {
            //     $filterKategoriArr = explode(',', $request->kategoriProduk);
            //     $produksQuery->whereHas('kategoriProduk', function ($query) use ($filterKategoriArr) {
            //         $query->whereIn('nama_kategori_produk', $filterKategoriArr);
            //     });
            // }

            // if ($request->statusProduk) {
            //     $filterStatusArr = explode(',', $request->statusProduk);
            //     $produksQuery->whereIn('status', $filterStatusArr);
            // }

            // // SEARCH
            // if ($request->search) {
            //     $produksQuery->where('nama_produk', 'like', '%' . $request->search . '%')
            //         ->orWhere('status', 'like', '%' . $request->search . '%')
            //         ->orWhereHas('kategoriProduk', function ($query) use ($request) {
            //             $query->where('nama_kategori_produk', 'like', '%' . $request->search . '%');
            //         })
            //         ->orWhereHas('penitip', function ($query) use ($request) {
            //             $query->where('nama_penitip', 'like', '%' . $request->search . '%');
            //         });
            // }

            // SORT METHOD #1
            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'nama_produk',
                'created_at',
            ])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            // SORT METHOD #2
            $arraySort = [
                'terbaru',
                'terlama',
            ];

            $arraySortValue = [
                ['name' => "Terbaru", 'sortBy' => "id", 'sortOrder' => "desc"],
                ['name' => "Terlama", 'sortBy' => "id", 'sortOrder' => "asc"],
            ];

            if ($request->sort && in_array(strtolower($request->sort), $arraySort)) {
                $key = array_search(strtolower($request->sort), $arraySort);
                $sortBy = $arraySortValue[$key]['sortBy'];
                $sortOrder = $arraySortValue[$key]['sortOrder'];
            }

            $produks = $produksQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $produks,
                    'message' => 'Berhasil mengambil data produk unique.'
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
