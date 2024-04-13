<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanBaku;

class BahanBakuController extends Controller
{
    //CRUDS biasa untuk bahan baku, terdiri dari index, show, store, update, destroy
    public function index() {
        $bahanBakus = BahanBaku::all();

        if($bahanBakus){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahanBakus
            ],200);
        }

        return reponse([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id) {
        $bahanBaku = BahanBaku::find($id);

        if($bahanBaku){
            return response([
                'message' => 'Retrieve Success',
                'data' => $bahanBaku
            ],200);
        }

        return response([
            'message' => 'ID Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request) {
        $bahanBaku = new BahanBaku;

        $bahanBaku->nama_bahan = $request->nama_bahan;
        $bahanBaku->stok = $request->stok;
        $bahanBaku->satuan = $request->satuan;
        $bahanBaku->harga = $request->harga;

        $bahanBaku->save();

        return response([
            'message' => 'Add Bahan Baku Success',
            'data' => $bahanBaku
        ],200);
    }

    public function update(Request $request, $id) {
        $bahanBaku = BahanBaku::find($id);

        if($bahanBaku){
            $bahanBaku->nama_bahan = $request->nama_bahan;
            $bahanBaku->stok = $request->stok;
            $bahanBaku->satuan = $request->satuan;
            $bahanBaku->harga = $request->harga;

            $bahanBaku->save();

            return response([
                'message' => 'Update Bahan Baku Success',
                'data' => $bahanBaku
            ],200);
        }

        return response([
            'message' => 'ID Not Found',
            'data' => null
        ],404);
    }

    public function destroy($id) {
        $bahanBaku = BahanBaku::find($id);

        if($bahanBaku){
            $bahanBaku->delete();

            return response([
                'message' => 'Delete Bahan Baku Success',
                'data' => $bahanBaku
            ],200);
        }

        return response([
            'message' => 'ID Not Found',
            'data' => null
        ],404);
    }
}
