<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\PenggunaanBahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PenggunaanBahanBakuController extends Controller
{
    public function addPenggunaan($object)
    {
        $validate = Validator::make($object, [
            'tanggal_penggunaan' => 'required',
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'jumlah_penggunaan' => 'required|numeric|min:1',
            'satuan_penggunaan' => 'required',
        ]);

        if ($validate->fails()) {
            return null;
        }

        $bahanBaku = BahanBaku::find($object['bahan_baku_id']);
        if (!$bahanBaku) {
            return null;
        }

        // create penggunaan
        $penggunaanData = PenggunaanBahanBaku::create($object);
        $penggunaanData = PenggunaanBahanBaku::query()
            ->with('bahanBaku')
            ->find($penggunaanData->id);

        // TODO:: kurangi stok bahan baku
        // update (kurangi) stok di bahan baku
        // karena penggunaan bahan baku berpengaruh terhadap stok bahan baku (-)
        $bahanBaku->jumlah_bahan_baku = $bahanBaku->jumlah_bahan_baku - $penggunaanData->jumlah_bahan;
        $bahanBaku->save();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $penggunaanQuery = PenggunaanBahanBaku::query()->with('bahanBaku');

            if ($request->search) {
                $penggunaanQuery
                    ->whereHas('bahanBaku', function ($query) use ($request) {
                        $query->where('nama_bahan_baku', 'like', '%' . $request->search . '%');
                    })
                    ->orWhere('jumlah_penggunaan', 'like', '%' . $request->search . '%')
                    ->orWhere('satuan_penggunaan', 'like', '%' . $request->search . '%')
                    ->orWhere('tanggal_penggunaan', 'like', '%' . $request->search . '%');
            }

            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'jumlah_penggunaan',
                'tanggal_penggunaan',
                'created_at'
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

            $penggunaanBahanBaku = $penggunaanQuery->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $penggunaanBahanBaku,
                    'message' => 'Berhasil mengambil data penggunaan bahan baku.'
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
