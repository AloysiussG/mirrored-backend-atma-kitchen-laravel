<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use Throwable;
use  Illuminate\Support\Facades\Validator;

class BahanBakuController extends Controller
{
    //CRUDS biasa untuk bahan baku, terdiri dari index, show, store, update, destroy
    public function index(Request $request) {
       try{
        $bahanBakus = BahanBaku::query();
        if ($request->search) {
            $bahanBakus->where('nama_bahan_baku', 'like', '%' . $request->search . '%')->orWhere('satuan_bahan', 'like', '%' . $request->search . '%');
        }

        if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_bahan_baku','jumlah_bahan_baku', 'created_at'])) {
            $sortBy = $request->sortBy;
        } else {
            $sortBy = 'id';
        }

        if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
            $sortOrder = $request->sortOrder;
        } else {
            $sortOrder = 'desc';
        }

        $bahanBakus = $bahanBakus->orderBy($sortBy, $sortOrder)->get();


        if($bahanBakus){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $bahanBakus
            ],200);
        }
       }
        catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }

    public function show($id) {
        try{
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
        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }

    public function store(Request $request) {
        try{
            $bahanBaku = new BahanBaku;
            $validate = Validator::make($request->all(), [
                'nama_bahan_baku' => 'required',
                'satuan_bahan' => 'required',
                'jumlah_bahan_baku' => 'required|numeric',
            ]);
            $bahanBaku->nama_bahan_baku = $request->nama_bahan_baku;
            $bahanBaku->satuan_bahan = $request->satuan_bahan;
            $bahanBaku->jumlah_bahan_baku = $request->jumlah_bahan_baku;

            $bahanBaku->save();

            return response([
                'message' => 'Add Bahan Baku Success',
                'data' => $bahanBaku
            ],200);
       }catch(Throwable $e){
        return response([
            'message' => $e->getMessage(),
            'data' => null
        ],500);
       }
    }

    public function update(Request $request, $id) {
        try{
            $bahanBaku = BahanBaku::find($id);
            $validate = Validator::make($request->all(), [
                'nama_bahan_baku' => 'required',
                'satuan_bahan' => 'required',
                'jumlah_bahan_baku' => 'required|numeric',
            ]);

        if($bahanBaku){
            $bahanBaku->nama_bahan_baku = $request->nama_bahan_baku;
            $bahanBaku->satuan_bahan = $request->satuan_bahan;
            $bahanBaku->jumlah_bahan_baku = $request->jumlah_bahan_baku;
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
        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function destroy($id) {
       try{
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
       }catch(Throwable $e){
        return response([
            'message' => $e->getMessage(),
            'data' => null
        ],500);
       }
    }
}
