<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailHampers;
use App\Models\Hampers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DetailHampersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexByHampers(Request $request, string $hampersId)
    {
        try {
            $hampersData = Hampers::find($hampersId);

            if (!$hampersData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            $detailHampersQuery = DetailHampers::query()
                ->with('produk')
                ->where('hampers_id', $hampersId);

            if ($request->produk) {
                $detailHampersQuery->whereHas('produk', function ($query) use ($request) {
                    $query->where('nama_produk', 'like', '%' . $request->produk . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'created_at'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $detailHampers = $detailHampersQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $detailHampers,
                    'message' => 'Berhasil mengambil data detail hampers dari hampers ' . $hampersData->nama_hampers . '.'
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
    public function store(Request $request, string $hampersId)
    {
        try {
            $hampersData = Hampers::find($hampersId);

            if (!$hampersData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            $detailHampersDataRequest = $request->all();

            $validate = Validator::make($detailHampersDataRequest, [
                'produk_id' => 'required|exists:produks,id',
                'jumlah_produk' => 'required',
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

            $detailHampersDataRequest['hampers_id'] = $hampersData->id;

            // create detail hampers
            // cek unik, dalam 1 hampers tidak boleh ada 2 produk yang sama namun beda jumlah produk
            // jika ada produk yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
            $detailHampers = DetailHampers::query()
                ->where('hampers_id', $hampersData->id)
                ->where('produk_id', $detailHampersDataRequest['produk_id'])
                ->first();

            if ($detailHampers) {
                $detailHampers->jumlah_produk = $detailHampers->jumlah_produk + $detailHampersDataRequest['jumlah_produk'];
                $detailHampers->save();
            } else {
                $detailHampers = DetailHampers::create($detailHampersDataRequest);
            }

            return response()->json(
                [
                    'data' => $detailHampers,
                    'message' => 'Berhasil membuat data detail hampers baru dari hampers ' . $hampersData->nama_hampers . '.'
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
            $detailHampersData = DetailHampers::with('produk')->find($id);

            if (!$detailHampersData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Detail hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $detailHampersData,
                    'message' => 'Berhasil mengambil 1 data detail hampers.',
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
            $detailHampersDataUpdated = DetailHampers::find($id);

            if (!$detailHampersDataUpdated) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Detail hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            $detailHampersDataRequest = $request->all();

            $validate = Validator::make($detailHampersDataRequest, [
                'hampers_id' => 'required|exists:hampers,id',
                'produk_id' => 'required|exists:produks,id',
                'jumlah_produk' => 'required',
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

            // update detail hampers
            $detailHampersDataUpdated->update($detailHampersDataRequest);

            return response()->json(
                [
                    'data' => $detailHampersDataUpdated,
                    'message' => 'Berhasil mengupdate data detail hampers.'
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
            $detailHampersDataDeleted = DetailHampers::find($id);

            if (!$detailHampersDataDeleted) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Detail hampers tidak ditemukan.',
                    ],
                    404
                );
            }

            if (!$detailHampersDataDeleted->delete()) {
                return response()->json(
                    [
                        'data' => $detailHampersDataDeleted,
                        'message' => 'Gagal menghapus data detail hampers.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $detailHampersDataDeleted,
                    'message' => 'Berhasil menghapus data detail hampers.',
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
