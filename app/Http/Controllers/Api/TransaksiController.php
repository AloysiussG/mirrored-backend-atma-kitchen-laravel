<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Throwable;

class TransaksiController extends Controller
{
    public function findByCustomer(Request $request){
        try{
            $transaksiQuery = Transaksi::query()->with(['cart.customer', 'statusTransaksi']);
            if($request->customer){
                $transaksiQuery->whereHas('cart.customer', function ($query) use ($request) {
                    $query->where('nama','like', '%'. $request->customer.'%');
                });
            }

            if($request->status){
                $transaksiQuery->whereHas('statusTransaksi', function ($query) use ($request) {
                    $query->where('nama_status','like', '%'. $request->status.'%');
                });
            }

            if($request->sortBy && in_array($request->sortBy, ['id','total_harga', 'status', 'tanggal_transaksi'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $transaksis = $transaksiQuery->orderBy($sortBy, $sortOrder)->get();

            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksis
            ],200);

        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }

    public function showWithProducts($id){
        try{
            $transaksi = Transaksi::with(['cart.detailCart.produk'])->find($id);
            return response([
                'message' => 'Retrieve Success',
                'data' => $transaksi
            ],200);
        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }
}
