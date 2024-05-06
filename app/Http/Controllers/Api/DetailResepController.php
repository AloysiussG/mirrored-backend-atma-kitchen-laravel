<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailResep;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DetailResepController extends Controller
{
    public function index(Request $request, string $id)
    {
        try {
            $detailResep = DetailResep::query()->with(['bahanBaku', 'resep'])->where('resep_id', $id);

            if ($request->search) {
                $detailResep->where('id', 'like', '%' . $request->search . '%')
                    ->orWhere('jumlah_bahan_resep', 'like', '%' . $request->search . '%')
                    ->orWhere('satuan_detail_resep', 'like', '%' . $request->search . '%')
                    ->orWhere('resep.nama_resep', 'like', '%' . $request->search . '%')
                    ->orWhere('bahanBaku.nama_bahan_baku', 'like', '%' . $request->search . '%');
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

            $resep = $detailResep->orderBy($sortBy, $sortOrder)->get();

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
            $detailResepDataRequest = $request->all();
            $validate = Validator::make($detailResepDataRequest, [
                'resep_id' => 'required|exists:reseps,id',
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'jumlah_bahan_resep' => 'required|gt:0',
            ], [
                'resep_id.required' => 'Resep harus dipilih.',
                'resep_id.exists' => 'Resep tidak ditemukan.',
                'bahan_baku_id.required' => 'Bahan baku harus dipilih.',
                'bahan_baku_id.exists' => 'Bahan baku tidak ditemukan.',
                'jumlah_bahan_resep.required' => 'Jumlah bahan harus diisi.',
                'jumlah_bahan_resep.gt' => 'Jumlah bahan tidak boleh kurang dari 0.',
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

            $resepData = Resep::find($request['resep_id']);

            if (!$resepData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Resep tidak ditemukan.',
                    ],
                    404
                );
            }

            $detailResepDataRequest['resep_id'] = $resepData->id;

            // create detail resep
            // cek unik, dalam 1 resep tidak boleh ada 2 bahan baku yang sama namun beda jumlah bahan baku
            // jika ada bahan baku yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
            $detailResep = DetailResep::query()
                ->where('resep_id', $resepData->id)
                ->where('bahan_baku_id', $detailResepDataRequest['bahan_baku_id'])
                ->first();

            if ($detailResep) {
                $detailResep->jumlah_bahan_resep = $detailResep->jumlah_bahan_resep + $detailResepDataRequest['jumlah_bahan_resep'];
                $detailResep->save();
            } else {
                $detailResep = DetailResep::create($detailResepDataRequest);
            }

            return response()->json(
                [
                    'data' => $detailResep,
                    'message' => 'Berhasil membuat data detail resep baru dari resep ' . $resepData->nama_resep . '.'
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
            $detailResep = DetailResep::with(['bahanBaku', 'resep'])->find($id);

            if (!$detailResep) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Detail resep tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $detailResep,
                    'message' => 'Berhasil mengambil 1 data detail resep.',
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
            $detailResepUpdate = DetailResep::find($id);

            if (!$detailResepUpdate) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Detail resep tidak ditemukan.',
                    ],
                    404
                );
            }
            $detailResepRequest = $request->all();

            $validate = Validator::make($detailResepRequest, [
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'jumlah_bahan_resep' => 'required|gt:0',
            ], [
                'bahan_baku_id.required' => 'Bahan baku tidak boleh kosong.',
                'bahan_baku_id.exists' => 'Bahan baku tidak ditemukan.',
                'jumlah_bahan_resep.required' => 'Jumlah bahan baku tidak boleh kosong.',
                'jumlah_bahan_resep.gt' => 'Jumlah bahan baku tidak boleh kurang dari 0.',
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

            $resepData = Resep::find($request['resep_id']);

            if (!$resepData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Resep tidak ditemukan.',
                    ],
                    404
                );
            }

            // create detail resep
            // cek unik, dalam 1 resep tidak boleh ada 2 bahan baku yang sama namun beda jumlah bahan baku
            // jika ada bahan baku yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya
            $detailResep = DetailResep::query()
                ->where('resep_id', $resepData->id)
                ->where('bahan_baku_id', $detailResepRequest['bahan_baku_id'])
                ->whereNotIn('id', [$id])
                ->first();

            if ($detailResep) {
                $detailResep->jumlah_bahan_resep = $detailResep->jumlah_bahan_resep + $detailResepUpdate['jumlah_bahan_resep'];
                $detailResep->save();
                $detailResepUpdate->delete();
            } else {
                $detailResepUpdate->update($detailResepRequest);
            }

            return response()->json(
                [
                    'data' => $detailResepUpdate,
                    'message' => 'Berhasil mengupdate data detail resep.'
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
            $detailResepDeleted = DetailResep::find($id);

            if (!$detailResepDeleted) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Detail resep tidak ditemukan.',
                    ],
                    404
                );
            }

            if (!$detailResepDeleted->delete()) {
                return response()->json(
                    [
                        'data' => $detailResepDeleted,
                        'message' => 'Gagal menghapus data detail resep.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $detailResepDeleted,
                    'message' => 'Berhasil menghapus data detail resep.',
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
