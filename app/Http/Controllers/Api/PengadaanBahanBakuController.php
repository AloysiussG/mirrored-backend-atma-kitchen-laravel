<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\PengadaanBahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

// Pengadaan Bahan Baku === Pembelian Bahan Baku (List Fungsionalitas 47-51)
class PengadaanBahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $pengadaanQuery = PengadaanBahanBaku::query()->with('bahanBaku');

            if ($request->search) {
                $pengadaanQuery
                    ->whereHas('bahanBaku', function ($query) use ($request) {
                        $query->where('nama_bahan_baku', 'like', '%' . $request->search . '%');
                    })
                    ->orWhere('jumlah_bahan', 'like', '%' . $request->search . '%')
                    ->orWhere('harga_pengadaan_bahan_baku', 'like', '%' . $request->search . '%')
                    ->orWhere('satuan_pengadaan', 'like', '%' . $request->search . '%')
                    ->orWhere('tanggal_pengadaan', 'like', '%' . $request->search . '%');
            }

            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'jumlah_bahan',
                'harga_pengadaan_bahan_baku',
                'tanggal_pengadaan',
                'created_at'
            ])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'tanggal_pengadaan';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $pengadaanBahanBaku = $pengadaanQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $pengadaanBahanBaku,
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $pengadaanRequest = $request->all();

            $validate = Validator::make($pengadaanRequest, [
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'jumlah_bahan' => 'required|numeric',
                'harga_pengadaan_bahan_baku' => 'required',
                'satuan_pengadaan' => 'required',
                'tanggal_pengadaan' => 'required',
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

            $bahanBaku = BahanBaku::find($pengadaanRequest['bahan_baku_id']);
            if (!$bahanBaku) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Bahan baku tidak ditemukan.',
                    ],
                    404
                );
            }

            // create pengadaan
            $pengadaanData = PengadaanBahanBaku::create($pengadaanRequest);
            $pengadaanData = PengadaanBahanBaku::query()
                ->with('bahanBaku')
                ->find($pengadaanData->id);

            // update (tambah) stok di bahan baku
            // karena pengadaan bahan baku berpengaruh terhadap stok bahan baku (+)
            $bahanBaku->jumlah_bahan_baku = $bahanBaku->jumlah_bahan_baku + $pengadaanData->jumlah_bahan;
            $bahanBaku->save();

            return response()->json(
                [
                    'data' => $pengadaanData,
                    'message' => 'Berhasil membuat data pengadaan bahan baku baru dan menambah stok bahan baku.',
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
            $pengadaanData = PengadaanBahanBaku::with('bahanBaku')->find($id);

            if (!$pengadaanData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Pengadaan bahan baku tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $pengadaanData,
                    'message' => 'Berhasil mengambil 1 data pengadaan bahan baku.',
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
            $pengadaanDataPrev = PengadaanBahanBaku::with('bahanBaku')->find($id);

            if (!$pengadaanDataPrev) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Pengadaan bahan baku tidak ditemukan.',
                    ],
                    404
                );
            }

            $bahanBakuPrev = $pengadaanDataPrev->bahanBaku;



            $pengadaanRequest = $request->all();

            $validate = Validator::make($pengadaanRequest, [
                'bahan_baku_id' => 'required|exists:bahan_bakus,id',
                'jumlah_bahan' => 'required|numeric',
                'harga_pengadaan_bahan_baku' => 'required',
                'satuan_pengadaan' => 'required',
                'tanggal_pengadaan' => 'required',
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



            $bahanBakuNext = BahanBaku::find($pengadaanRequest['bahan_baku_id']);
            if (!$bahanBakuNext) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Bahan baku tidak ditemukan.',
                    ],
                    404
                );
            }

            // update stok di bahan baku
            // karena pengadaan bahan baku berpengaruh terhadap stok bahan baku
            // 
            // dilakukan 3 cara: 
            // 1. mengcancel / undo penambahan stok di bahan baku prev (-), mengurangi stok bahan baku prev dengan value pengadaan prev
            // 2. mengupdate data pengadaan lama menjadi data pengadaan baru
            // 3. menambah stok di bahan baku next (+), menambah stok bahan baku next dengan value pengadaan next/request current

            // 1. undo pengadaan sebelumnya, dan undo stok bahan baku sebelumnya (-) 
            //    menggunakan bahan baku prev yang disimpan sejak awal (sblm update)
            $bahanBakuPrev->jumlah_bahan_baku = $bahanBakuPrev->jumlah_bahan_baku - $pengadaanDataPrev->jumlah_bahan;
            $bahanBakuPrev->save();

            // 2. update pengadaan sebelumnya menjadi data pengadaan sekarang/pengadaan request 
            $pengadaanDataNext = $pengadaanDataPrev;
            $pengadaanDataNext->update($pengadaanRequest);

            // 3. cari data bahan baku berdasarkan pengadaan bahan baku terbaru, lalu tambah ulang (+)
            $bahanBakuNext = BahanBaku::find($pengadaanDataNext->bahan_baku_id);
            $bahanBakuNext->jumlah_bahan_baku = $bahanBakuNext->jumlah_bahan_baku + $pengadaanDataNext->jumlah_bahan;
            $bahanBakuNext->save();

            // refresh untuk mendapatkan data pengadaan + bahan baku terbaru
            $pengadaanDataNext = PengadaanBahanBaku::with('bahanBaku')->find($id);

            return response()->json(
                [
                    'data' => $pengadaanDataNext,
                    'message' => 'Berhasil mengupdate data pengadaan bahan baku dan mengupdate kedua stok bahan baku.',
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
            $pengadaanDataPrev = PengadaanBahanBaku::with('bahanBaku')->find($id);

            if (!$pengadaanDataPrev) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Pengadaan bahan baku tidak ditemukan.',
                    ],
                    404
                );
            }

            $bahanBakuPrev = $pengadaanDataPrev->bahanBaku;

            // undo stok bahan baku akibat pengadaan sebelumnya (-)
            $bahanBakuPrev->jumlah_bahan_baku = $bahanBakuPrev->jumlah_bahan_baku - $pengadaanDataPrev->jumlah_bahan;
            $bahanBakuPrev->save();

            // setelah undo stok bahan baku, baru delete pengadaan bahan bakunya
            if (!$pengadaanDataPrev->delete()) {
                return response()->json(
                    [
                        'data' => $pengadaanDataPrev,
                        'message' => 'Gagal menghapus data pengadaan bahan baku.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $pengadaanDataPrev,
                    'message' => 'Berhasil menghapus data pengadaan bahan baku.',
                ],
                200
            );
        } catch (\Throwable $th) {
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
