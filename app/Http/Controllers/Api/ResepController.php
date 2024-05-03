<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailResep;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ResepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $resepQuery = Resep::query()->with(['detailResep.bahanBaku', 'produk']);

            if ($request->search) {
                $resepQuery->where('nama_resep', 'like', '%' . $request->search . '%');
            }

            if ($request->bahan_baku) {
                $resepQuery->whereHas('detailResep.bahanBaku', function ($query) use ($request) {
                    $query->where('nama_bahan_baku', 'like', '%' . $request->bahan_baku . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'produk_id', 'nama_resep','created_at'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $resep = $resepQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $resep,
                    'message' => 'Berhasil mengambil data resep.'
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
            $resepDataRequest = $request->all();

            $validate = Validator::make($resepDataRequest, [
                'nama_resep' => 'required',
                'produk_id' => 'required|exists:produks,id',
            ],[
                'produk_id.required' => 'Produk harus dipilih.',
                'produk_id.exists' => 'Produk tidak ditemukan.',
                'nama_resep.required' => 'Nama resep harus diisi.',
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

            // create resep
            $resepData = Resep::create($resepDataRequest);

            // create detail resep dengan foreach loop (detail resep harus berupa array)
            foreach ($resepDataRequest['detail_resep'] as $value) {
                $value['resep_id'] = $resepData->id;

                // cek unik, dalam 1 resep tidak boleh ada 2 bahan baku yang sama namun beda jumlah bahan baku
                // jika ada resep yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
                $detailResep = DetailResep::query()
                    ->where('resep_id', $resepData->id)
                    ->where('bahan_baku_id', $value['bahan_baku_id'])
                    ->first();

                if ($detailResep) {
                    $detailResep->jumlah_bahan_resep = $detailResep->jumlah_bahan_resep + $value['jumlah_bahan_resep'];
                    $detailResep->save();
                } else {
                    DetailResep::create($value);
                }
            }

            $resepData = Resep::query()
                ->with('detailResep.bahanBaku')
                ->find($resepData->id);

            return response()->json(
                [
                    'data' => $resepData,
                    'message' => 'Berhasil membuat data resep baru.',
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
            $resepData = Resep::with(['detailResep.bahanBaku', 'produk'])->find($id);

            if (!$resepData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Resep tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $resepData,
                    'message' => 'Berhasil mengambil 1 data Resep.',
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
            $resepUpdate = Resep::find($id);

            if (!$resepUpdate) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Resep tidak ditemukan.',
                    ],
                    404
                );
            }

            $resepDataRequest = $request->all();

            $validate = Validator::make($resepDataRequest, [
                'produk_id' => 'required|exists:produks,id',
                'nama_resep' => 'required',   
            ], [
                'produk_id.required' => 'Produk harus dipilih.',
                'produk_id.exists' => 'Produk tidak ditemukan.',
                'nama_resep.required' => 'Nama resep harus diisi.',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->errors()->first(),
                    ],
                    400
                );
            }

            // update resep
            $resepUpdate->update($resepDataRequest);

            return response()->json(
                [
                    'data' => $resepUpdate,
                    'message' => 'Berhasil mengupdate data resep.',
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
            $resepDeleted = Resep::find($id);
            if (!$resepDeleted) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Resep tidak ditemukan.',
                    ],
                    404
                );
            }

            $detailResep = DetailResep::where('resep_id', $resepDeleted->id)->get();
            foreach($detailResep as $detail){
                $detail->delete();
            }

            if (!$resepDeleted->delete()) {
                return response()->json(
                    [
                        'data' => $resepDeleted,
                        'message' => 'Gagal menghapus data Resep.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $resepDeleted,
                    'message' => 'Berhasil menghapus data Resep.',
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
