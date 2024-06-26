<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailHampers;
use App\Models\Hampers;
use App\Models\Packaging;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class HampersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $hampersQuery = Hampers::query()->with('detailHampers.produk', 'packagings.bahanBaku');

            if ($request->search) {
                $hampersQuery->where('nama_hampers', 'like', '%' . $request->search . '%');
            }

            if ($request->produk) {
                $hampersQuery->whereHas('detailHampers.produk', function ($query) use ($request) {
                    $query->where('nama_produk', 'like', '%' . $request->produk . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_hampers', 'created_at', 'harga_hampers'])) {
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
                'harga tertinggi',
                'harga terendah',
                'stok terbanyak',
                'kuota terbanyak',
            ];

            $arraySortValue = [
                ['name' => "Terbaru", 'sortBy' => "id", 'sortOrder' => "desc"],
                ['name' => "Terlama", 'sortBy' => "id", 'sortOrder' => "asc"],
                ['name' => "Harga tertinggi", 'sortBy' => "harga_hampers", 'sortOrder' => "desc"],
                ['name' => "Harga terendah", 'sortBy' => "harga_hampers", 'sortOrder' => "asc"],
            ];

            if ($request->sort && in_array(strtolower($request->sort), $arraySort)) {
                $key = array_search(strtolower($request->sort), $arraySort);
                $sortBy = $arraySortValue[$key]['sortBy'];
                $sortOrder = $arraySortValue[$key]['sortOrder'];
            }

            $hampers = $hampersQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $hampers,
                    'message' => 'Berhasil mengambil data hampers.'
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

    // ---- GUIDE INPUTAN SAAT STORE HAMPERS
    // {
    //     "nama_hampers": "HampersKedua",
    //     "harga_hampers": 324000,
    //     "detail_hampers": [
    //         {
    //             "produk_id": 5,
    //             "jumlah_produk": 15
    //         },
    //         {
    //             "produk_id": 6,
    //             "jumlah_produk": 20
    //         }
    //     ]
    // }

    public function store(Request $request)
    {
        try {
            $hampersDataRequest = $request->all();

            $validate = Validator::make($hampersDataRequest, [
                'nama_hampers' => 'required',
                'harga_hampers' => 'required|numeric|min:1000',
                'detail_hampers' => 'required|array',
                'detail_hampers.*.produk_id' => 'required|exists:produks,id',
                'detail_hampers.*.jumlah_produk' => 'required|numeric|min:1',
                'foto_hampers' => 'required|image:jpeg,png,jpg,gif,svg|max:4096',
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

            // jika ada request image
            if ($request->file('foto_hampers')) {
                $uploadFolder = '/hampers';

                $fileImage = $hampersDataRequest['foto_hampers'];
                $imageUploadedPath = $fileImage->store($uploadFolder, 'public');

                // ambil url image yang disimpan di storage link
                // lalu masukkan ke db
                // $imageURL = Storage::url($imageUploadedPath);
                $hampersDataRequest['foto_hampers'] = $imageUploadedPath;
            }

            // create hampers
            $hampersData = Hampers::create($hampersDataRequest);

            // create detail hampers dengan foreach loop (detail hampers harus berupa array)
            foreach ($hampersDataRequest['detail_hampers'] as $value) {
                // assign detail hampers ke hampers, lalu create detail hampers
                $value['hampers_id'] = $hampersData->id;

                // cek unik, dalam 1 hampers tidak boleh ada 2 produk yang sama namun beda jumlah produk
                // jika ada produk yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                $detailHampers = DetailHampers::query()
                    ->where('hampers_id', $hampersData->id)
                    ->where('produk_id', $value['produk_id'])
                    ->first();

                if ($detailHampers) {
                    $detailHampers->jumlah_produk = $detailHampers->jumlah_produk + $value['jumlah_produk'];
                    $detailHampers->save();
                } else {
                    DetailHampers::create($value);
                }
            }

            // --- CREATE PACKAGING ---
            // create packaging dengan foreach loop (packaging harus berupa array)
            foreach ($hampersDataRequest['packagings'] as $value) {
                // assign packaging ke produk, lalu create packaging
                $value['hampers_id'] = $hampersData->id;

                // cek unik, dalam 1 produk tidak boleh ada 2 bahan baku Packaging yang sama namun beda jumlah bahan baku Packaging
                // jika ada bahan baku Packaging yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                $packaging = Packaging::query()
                    ->where('hampers_id', $hampersData->id)
                    ->where('bahan_baku_id', $value['bahan_baku_id'])
                    ->first();

                if ($packaging) {
                    $packaging->jumlah = $packaging->jumlah + $value['jumlah'];
                    $packaging->save();
                } else {
                    Packaging::create($value);
                }
            }

            $hampersData = Hampers::query()
                ->with('detailHampers.produk', 'packagings.bahanBaku')
                ->find($hampersData->id);

            return response()->json(
                [
                    'data' => $hampersData,
                    'message' => 'Berhasil membuat data hampers baru.',
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
            $hampersData = Hampers::with('detailHampers.produk', 'packagings.bahanBaku')->find($id);

            if (!$hampersData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $hampersData,
                    'message' => 'Berhasil mengambil 1 data hampers.',
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

    // ---- GUIDE INPUTAN SAAT UPDATEALL HAMPERS
    // {
    //     "nama_hampers": "HampersKedua123",
    //     "harga_hampers": 125000,
    //     "detail_hampers": [
    //         {
    //             "produk_id": 7,
    //             "jumlah_produk": 15      ---------
    //         },                                   |
    //         {                                    |
    //             "produk_id": 6,                  |
    //             "jumlah_produk": 18              |------> 15 + 78 = 93
    //         },                                   |
    //         {                                    |
    //             "produk_id": 7,                  |
    //             "jumlah_produk": 78      ---------
    //         }
    //     ]
    // }

    public function updateAll(Request $request, string $id)
    {
        try {
            $hampersDataUpdated = Hampers::find($id);

            if (!$hampersDataUpdated) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            $hampersDataRequest = $request->all();

            $validate = Validator::make($hampersDataRequest, [
                'nama_hampers' => 'required',
                'harga_hampers' => 'required|numeric|min:1000',
                'detail_hampers' => 'required|array',
                'detail_hampers.*.produk_id' => 'required|exists:produks,id',
                'detail_hampers.*.jumlah_produk' => 'required|numeric|min:1',
                'foto_hampers' => 'image:jpeg,png,jpg,gif,svg|max:4096',
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

            // jika ada request image
            if ($request->file('foto_hampers')) {
                $uploadFolder = '/hampers';

                $fileImage = $hampersDataRequest['foto_hampers'];
                $imageUploadedPath = $fileImage->store($uploadFolder, 'public');

                // ambil url image yang disimpan di storage link
                // lalu masukkan ke db
                // $imageURL = Storage::url($imageUploadedPath);
                $hampersDataRequest['foto_hampers'] = $imageUploadedPath;

                // delete image lama di storage ketika berhasil set image baru
                if (!is_null($hampersDataUpdated->foto_hampers) && Storage::disk('public')->exists($hampersDataUpdated->foto_hampers)) {
                    Storage::disk('public')->delete($hampersDataUpdated->foto_hampers);
                }
            }

            // update hampers
            $hampersDataUpdated->update($hampersDataRequest);

            // update detail hampers
            // dengan cara: delete all detail hampers terlebih dahulu, baru create lagi dari awal
            // create detail hampers dengan foreach loop (detail hampers harus berupa array)
            $detailHampersDataDeleted = DetailHampers::query()
                ->where('hampers_id', $hampersDataUpdated->id);

            $detailHampersDataDeleted->delete();

            foreach ($hampersDataRequest['detail_hampers'] as $value) {
                // assign detail hampers ke hampers, lalu create detail hampers
                $value['hampers_id'] = $hampersDataUpdated->id;

                // cek unik, dalam 1 hampers tidak boleh ada 2 produk yang sama namun beda jumlah produk
                // jika ada produk yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                $detailHampers = DetailHampers::query()
                    ->where('hampers_id', $hampersDataUpdated->id)
                    ->where('produk_id', $value['produk_id'])
                    ->first();

                if ($detailHampers) {
                    $detailHampers->jumlah_produk = $detailHampers->jumlah_produk + $value['jumlah_produk'];
                    $detailHampers->save();
                } else {
                    DetailHampers::create($value);
                }
            }

            // update packaging
            // dengan cara: delete all packaging terlebih dahulu, baru create lagi dari awal
            // create packaging dengan foreach loop (packaging harus berupa array) 
            $packagingsDataDeleted = Packaging::query()
                ->where('hampers_id', $hampersDataUpdated->id);

            $packagingsDataDeleted->delete();

            // --- CREATE PACKAGING ---
            // create packaging dengan foreach loop (packaging harus berupa array)
            if (isset($hampersDataRequest['packagings'])) {
                foreach ($hampersDataRequest['packagings'] as $value) {
                    // assign packaging ke produk, lalu create packaging
                    $value['hampers_id'] = $hampersDataUpdated->id;

                    // cek unik, dalam 1 produk tidak boleh ada 2 bahan baku Packaging yang sama namun beda jumlah bahan baku Packaging
                    // jika ada bahan baku Packaging yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                    $packaging = Packaging::query()
                        ->where('hampers_id', $hampersDataUpdated->id)
                        ->where('bahan_baku_id', $value['bahan_baku_id'])
                        ->first();

                    if ($packaging) {
                        $packaging->jumlah = $packaging->jumlah + $value['jumlah'];
                        $packaging->save();
                    } else {
                        Packaging::create($value);
                    }
                }
            }

            $hampersDataUpdated = Hampers::query()
                ->with('detailHampers.produk', 'packagings.bahanBaku')
                ->find($hampersDataUpdated->id);

            return response()->json(
                [
                    'data' => $hampersDataUpdated,
                    'message' => 'Berhasil mengupdate data hampers dan detail hampers.',
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
            $hampersDataDeleted = Hampers::find($id);

            if (!$hampersDataDeleted) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            if (!$hampersDataDeleted->delete()) {
                // delete image lama di storage ketika berhasil set image baru
                if (!is_null($hampersDataDeleted->foto_hampers) && Storage::disk('public')->exists($hampersDataDeleted->foto_hampers)) {
                    Storage::disk('public')->delete($hampersDataDeleted->foto_hampers);
                }

                return response()->json(
                    [
                        'data' => $hampersDataDeleted,
                        'message' => 'Gagal menghapus data hampers.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $hampersDataDeleted,
                    'message' => 'Berhasil menghapus data hampers.',
                ],
                200
            );
        } catch (Throwable $th) {
            if ($th->errorInfo[0] == 23000 && $th->errorInfo[1] == 1451) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Hampers tidak dapat dihapus karena sudah pernah ditransaksikan.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage(),
                    'exception' => $th
                ],
                500
            );
        }
    }
}
