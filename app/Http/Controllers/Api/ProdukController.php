<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Packaging;
use App\Models\Penitip;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $produksQuery = Produk::query()->with('kategoriProduk', 'penitip', 'packagings.bahanBaku');

            if ($request->search) {
                $produksQuery->where('nama_produk', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhereHas('kategoriProduk', function ($query) use ($request) {
                        $query->where('nama_kategori_produk', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('penitip', function ($query) use ($request) {
                        $query->where('nama_penitip', 'like', '%' . $request->search . '%');
                    });
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

            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'nama_produk',
                'created_at',
                'harga',
                'jumlah_stock',
                'kuota_harian'
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

            if ($kategoriProduk->nama_kategori_produk === 'Titipan') {
                $produkDataRequest['status'] = 'Ready Stock';
            }

            $validate = Validator::make($produkDataRequest, [
                'kategori_produk_id' => 'required|exists:kategori_produks,id',
                'nama_produk' => 'required',
                'harga' => 'required|numeric|min:1000',
                'kuota_harian' => 'required|numeric|min:1',
                'penitip_id' => [
                    Rule::requiredIf(function () use ($kategoriProduk) {
                        return $kategoriProduk->nama_kategori_produk === 'Titipan';
                    }),
                    'nullable',
                    'exists:penitips,id',
                ],
                'status' => [
                    'required',
                    Rule::in(
                        $kategoriProduk->nama_kategori_produk === 'Titipan' ? ['Ready Stock'] : ['Pre Order', 'Ready Stock']
                    )
                ],
                'jumlah_stock' => [
                    // jumlah stock required untuk ready stock, kalo PO opsional
                    Rule::requiredIf(fn () => $produkDataRequest['status'] === 'Ready Stock'),
                    'nullable',
                    'numeric',
                    // kalau ready stock, jumlah stok harus > 0, kalau PO boleh 0
                    $produkDataRequest['status'] === 'Ready Stock' ? 'min:1' : 'min:0',
                ],
                'porsi' => [
                    Rule::requiredIf(function () use ($kategoriProduk) {
                        return $kategoriProduk->nama_kategori_produk === 'Cake';
                    }),
                    'nullable',
                    'numeric',
                ],
                'foto_produk' => 'required|image:jpeg,png,jpg,gif,svg|max:4096',
                // ::: accept packagings :::
                'packagings' => 'required|array',
                'packagings.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'packagings.*.jumlah' => 'required|numeric|min:1',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages()->first(),
                    ],
                    400
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

            // jika ada request image
            if ($request->file('foto_produk')) {
                $uploadFolder = '/produk';

                $fileImage = $produkDataRequest['foto_produk'];
                $imageUploadedPath = $fileImage->store($uploadFolder, 'public');

                // ambil url image yang disimpan di storage link
                // lalu masukkan ke db
                // $imageURL = Storage::url($imageUploadedPath);
                $produkDataRequest['foto_produk'] = $imageUploadedPath;
            }

            $produkData = Produk::create($produkDataRequest);

            // --- CREATE PACKAGING ---
            // create packaging dengan foreach loop (packaging harus berupa array)
            foreach ($produkDataRequest['packagings'] as $value) {
                // assign packaging ke produk, lalu create packaging
                $value['produk_id'] = $produkData->id;

                // cek unik, dalam 1 produk tidak boleh ada 2 bahan baku Packaging yang sama namun beda jumlah bahan baku Packaging
                // jika ada bahan baku Packaging yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                $packaging = Packaging::query()
                    ->where('produk_id', $produkData->id)
                    ->where('bahan_baku_id', $value['bahan_baku_id'])
                    ->first();

                if ($packaging) {
                    $packaging->jumlah = $packaging->jumlah + $value['jumlah'];
                    $packaging->save();
                } else {
                    Packaging::create($value);
                }
            }

            $produkData = Produk::query()
                ->with('kategoriProduk', 'penitip', 'packagings.bahanBaku')
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
            $produkData = Produk::with('kategoriProduk', 'penitip', 'packagings.bahanBaku')->find($id);

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

            // $validate = Validator::make($produkDataRequest, [
            //     'kategori_produk_id' => 'required',
            //     'nama_produk' => 'required',
            //     'status' => 'required',
            //     'harga' => 'required',
            //     'kuota_harian' => 'required',
            //     'foto_produk' => 'image:jpeg,png,jpg,gif,svg|max:4096',
            // ]);

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

            if ($kategoriProduk->nama_kategori_produk === 'Titipan') {
                $produkDataRequest['status'] = 'Ready Stock';
            }

            $validate = Validator::make($produkDataRequest, [
                'kategori_produk_id' => 'required|exists:kategori_produks,id',
                'nama_produk' => 'required',
                'harga' => 'required|numeric|min:1000',
                'kuota_harian' => 'required|numeric|min:1',
                'penitip_id' => [
                    Rule::requiredIf(function () use ($kategoriProduk) {
                        return $kategoriProduk->nama_kategori_produk === 'Titipan';
                    }),
                    'nullable',
                    'exists:penitips,id',
                ],
                'status' => [
                    'required',
                    Rule::in(
                        $kategoriProduk->nama_kategori_produk === 'Titipan' ? ['Ready Stock'] : ['Pre Order', 'Ready Stock']
                    )
                ],
                'jumlah_stock' => [
                    // jumlah stock required untuk ready stock, kalo PO opsional
                    Rule::requiredIf(fn () => $produkDataRequest['status'] === 'Ready Stock'),
                    'nullable',
                    'numeric',
                    // kalau ready stock, jumlah stok harus > 0, kalau PO boleh 0
                    $produkDataRequest['status'] === 'Ready Stock' ? 'min:1' : 'min:0',
                ],
                'porsi' => [
                    Rule::requiredIf(function () use ($kategoriProduk) {
                        return $kategoriProduk->nama_kategori_produk === 'Cake';
                    }),
                    'nullable',
                    'numeric',
                ],
                'foto_produk' => 'image:jpeg,png,jpg,gif,svg|max:4096',
                // ::: accept packagings :::
                'packagings' => 'required|array',
                'packagings.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'packagings.*.jumlah' => 'required|numeric|min:1',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages()->first(),
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

            // jika ada request image
            if ($request->file('foto_produk')) {
                $uploadFolder = '/produk';

                $fileImage = $produkDataRequest['foto_produk'];
                $imageUploadedPath = $fileImage->store($uploadFolder, 'public');

                // ambil url image yang disimpan di storage link
                // lalu masukkan ke db
                // $imageURL = Storage::url($imageUploadedPath);
                $produkDataRequest['foto_produk'] = $imageUploadedPath;

                // delete image lama di storage ketika berhasil set image baru
                if (!is_null($produkDataUpdated->foto_produk) && Storage::disk('public')->exists($produkDataUpdated->foto_produk)) {
                    Storage::disk('public')->delete($produkDataUpdated->foto_produk);
                }
            }

            $produkDataUpdated->update($produkDataRequest);

            // update packaging
            // dengan cara: delete all packaging terlebih dahulu, baru create lagi dari awal
            // create packaging dengan foreach loop (packaging harus berupa array) 
            $packagingsDataDeleted = Packaging::query()
                ->where('produk_id', $produkDataUpdated->id);

            $packagingsDataDeleted->delete();

            // --- CREATE PACKAGING ---
            // create packaging dengan foreach loop (packaging harus berupa array)
            foreach ($produkDataRequest['packagings'] as $value) {
                // assign packaging ke produk, lalu create packaging
                $value['produk_id'] = $produkDataUpdated->id;

                // cek unik, dalam 1 produk tidak boleh ada 2 bahan baku Packaging yang sama namun beda jumlah bahan baku Packaging
                // jika ada bahan baku Packaging yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                $packaging = Packaging::query()
                    ->where('produk_id', $produkDataUpdated->id)
                    ->where('bahan_baku_id', $value['bahan_baku_id'])
                    ->first();

                if ($packaging) {
                    $packaging->jumlah = $packaging->jumlah + $value['jumlah'];
                    $packaging->save();
                } else {
                    Packaging::create($value);
                }
            }

            $produkDataUpdated = Produk::query()
                ->with('kategoriProduk', 'penitip', 'packagings.bahanBaku')
                ->find($produkDataUpdated->id);

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
                if (!is_null($produkDataDeleted->foto_produk) && Storage::disk('public')->exists($produkDataDeleted->foto_produk)) {
                    Storage::disk('public')->delete($produkDataDeleted->foto_produk);
                }
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
            if ($th->errorInfo[0] == 23000 && $th->errorInfo[1] == 1451) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Produk tidak dapat dihapus karena sudah pernah ditransaksikan.',
                    ],
                    500
                );
            }

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
