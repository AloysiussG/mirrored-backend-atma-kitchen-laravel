<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailHampers;
use App\Models\Hampers;
use App\Models\Produk;
use Illuminate\Http\Request;
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
            $hampersQuery = Hampers::query()->with('detailHampers.produk');

            if ($request->search) {
                $hampersQuery->where('nama_hampers', 'like', '%' . $request->search . '%');
            }

            if ($request->produk) {
                $hampersQuery->whereHas('detailHampers.produk', function ($query) use ($request) {
                    $query->where('nama_produk', 'like', '%' . $request->produk . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_hampers', 'created_at'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
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
                'harga_hampers' => 'required',
                'detail_hampers' => 'required|array',
                'detail_hampers.*.produk_id' => 'required|exists:produks,id',
                'detail_hampers.*.jumlah_produk' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages(),
                    ],
                    400
                );
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

            $hampersData = Hampers::query()
                ->with('detailHampers.produk')
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
            $hampersData = Hampers::with('detailHampers.produk')->find($id);

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
                'harga_hampers' => 'required',
                'detail_hampers' => 'required|array',
                'detail_hampers.*.produk_id' => 'required|exists:produks,id',
                'detail_hampers.*.jumlah_produk' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages(),
                    ],
                    400
                );
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

            $hampersDataUpdated = Hampers::query()
                ->with('detailHampers.produk')
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
