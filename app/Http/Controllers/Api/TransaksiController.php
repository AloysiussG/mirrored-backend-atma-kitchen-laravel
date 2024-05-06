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
            $transaksiQuery = Transaksi::query()->with(['cart.customer', 'statusTransaksi', 'packagings.bahanBaku']);
            if($request->search){
                $transaksiQuery->whereHas('cart.customer', function ($query) use ($request) {
                    $query->where('nama','like', '%'. $request->search.'%');
                })->orwhereHas('statusTransaksi', function ($query) use ($request) {
                    $query->where('nama_status','like', '%'. $request->search.'%');
                })->orWhere('no_nota','like', '%'. $request->search.'%');
            }
            if($request->date){
                $transaksiQuery->whereDate('tanggal_pesan',$request->date);
            }

            if($request->status){
                $transaksiQuery->where('status_transaksi_id',$request->status);
            }

            if($request->sortBy && in_array($request->sortBy, ['id','total_harga', 'status', 'tanggal_pesan'])) {
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
            $transaksi = Transaksi::with(['cart.detailCart.produk','cart.customer', 'cart.detailCart.hampers', 'statusTransaksi','alamat','packagings.bahanBaku'])->find($id);
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
    
    // nanti sewaktu transaksi === diproses
    // jangan lupa tambah packaging 1x Tas Spunbond
    // kurangi stok bahan baku Tas Spunbond
}
