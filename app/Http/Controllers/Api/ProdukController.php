<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Penitip;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $produksQuery = Produk::query()->with('kategoriProduk', 'penitip');

            if ($request->search) {
                $produksQuery->where('nama_produk', 'like', '%' . $request->search . '%');
            }

            if ($request->status) {
                $produksQuery->where('status', 'like', '%' . $request->status . '%');
            }

            if ($request->kategori) {
                $produksQuery->whereHas('kategoriProduk', function ($query) use ($request) {
                    $query->where('nama_kategori_produk', 'like', '%' . $request->kategori . '%');
                });
            }

            if ($request->penitip) {
                $produksQuery->whereHas('penitip', function ($query) use ($request) {
                    $query->where('nama_penitip', 'like', '%' . $request->penitip . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_produk', 'created_at', 'harga'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $produks = $produksQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $produks,
                    'message' => 'Berhasil mengambil data produk.'
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
    public function indexByKategoriProduk()
    {
        try {
            $produks = KategoriProduk::query()
                ->with('produks')
                ->latest()
                ->get();

            return response()->json(
                [
                    'data' => $produks,
                    'message' => 'Berhasil mengambil data produk berdasarkan kategori produk.'
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
        try {
            $produkDataRequest = $request->all();

            $validate = Validator::make($produkDataRequest, [
                'kategori_produk_id' => 'required',
                'nama_produk' => 'required',
                'status' => 'required',
                'harga' => 'required',
                'kuota_harian' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data produk tidak valid.',
                    ],
                    400
                );
            }

            $kategoriProduk = KategoriProduk::find($produkDataRequest['kategori_produk_id']);
            if (!$kategoriProduk) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Kategori produk tidak ditemukan.',
                    ],
                    404
                );
            }

            if ($produkDataRequest['penitip_id']) {
                $penitip = Penitip::find($produkDataRequest['penitip_id']);
                if (!$penitip) {
                    return response()->json(
                        [
                            'data' => null,
                            'message' => 'Penitip tidak ditemukan.',
                        ],
                        404
                    );
                }
            }

            $produkData = Produk::create($produkDataRequest);
            $produkData = Produk::query()
                ->with('kategoriProduk', 'penitip')
                ->find($produkData->id);

            return response()->json(
                [
                    'data' => $produkData,
                    'message' => 'Berhasil membuat data produk baru.',
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $produkData = Produk::with('kategoriProduk', 'penitip')->find($id);

            if (!$produkData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Produk tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $produkData,
                    'message' => 'Berhasil mengambil 1 data produk.',
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $produkDataUpdated = Produk::find($id);

            if (!$produkDataUpdated) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Produk tidak ditemukan.',
                    ],
                    404
                );
            }

            $produkDataRequest = $request->all();

            $validate = Validator::make($produkDataRequest, [
                'kategori_produk_id' => 'required',
                'nama_produk' => 'required',
                'status' => 'required',
                'harga' => 'required',
                'kuota_harian' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data produk tidak valid.',
                    ],
                    400
                );
            }

            $kategoriProduk = KategoriProduk::find($produkDataRequest['kategori_produk_id']);
            if (!$kategoriProduk) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Kategori produk tidak ditemukan.',
                    ],
                    404
                );
            }

            if ($produkDataRequest['penitip_id']) {
                $penitip = Penitip::find($produkDataRequest['penitip_id']);
                if (!$penitip) {
                    return response()->json(
                        [
                            'data' => null,
                            'message' => 'Penitip tidak ditemukan.',
                        ],
                        404
                    );
                }
            }

            $produkDataUpdated->update($produkDataRequest);

            return response()->json(
                [
                    'data' => $produkDataUpdated,
                    'message' => 'Berhasil mengupdate data produk.',
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $produkDataDeleted = Produk::find($id);

            if (!$produkDataDeleted) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Produk tidak ditemukan.',
                    ],
                    404
                );
            }

            if (!$produkDataDeleted->delete()) {
                return response()->json(
                    [
                        'data' => $produkDataDeleted,
                        'message' => 'Gagal menghapus data produk.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $produkDataDeleted,
                    'message' => 'Berhasil menghapus data produk.',
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
