<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PermintaanRefund;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Validator;

class PermintaaanRefundController extends Controller
{
    public function indexByCustomer($id){
        try{
            $pengembalian_saldo = PermintaanRefund::where('customer_id', $id)->get();
            return response()->json([
                'message' => 'success mengambil data pengembalian saldo customer id '.$id,
                'data' => $pengembalian_saldo
            ]);
        } catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }

    public function indexByStatus(){
        //status nya cuma pending sama berhasil setelah dikirim permintaaan nya
        try{
            $pengembalian_saldo = PermintaanRefund::where('status', 'pending')->get();
            return response()->json([
                'message' => 'success mengambil data pengembalian saldo dengan status pending',
                'data' => $pengembalian_saldo
            ]);
        } catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }

    public function kirimRequest(Request $request){
        try{
            $pengembalian_saldo = new PermintaanRefund();
            $customer = Customer::find($request->customer_id);

            if(!$customer){
                return response()->json([
                    'message' => 'customer tidak ditemukan',
                    'data' => null
                ],404);
            }

           $validate = Validator::make($request->all(), [
                'customer_id' => 'required',
                'nominal' => 'required | max:'.$customer->saldo,
            ]);

            if($validate->fails()){
                return response()->json([
                    'message' => $validate->errors(),
                    'data' => null
                ],400);
            }

            $pengembalian_saldo->customer_id = $request->customer_id;
            $pengembalian_saldo->status = 'pending';
            $pengembalian_saldo->nominal = $request->nominal;
            $pengembalian_saldo->tanggal_refund = Carbon::now();
            $pengembalian_saldo->tanggal_proses = null;
            $pengembalian_saldo->save();

            $customer->saldo -= $request->nominal;
            $customer->save();

            return response()->json([
                'message' => 'success menambahkan data pengembalian saldo',
                'data' => $pengembalian_saldo
            ]);

        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }

    public function terimaRequest($id){
         //status nya cuma pending sama berhasil setelah dikirim permintaaan nya
        try{
            $pengembalian_saldo = PermintaanRefund::find($id);

            if(!$pengembalian_saldo){
                return response()->json([
                    'message' => 'data pengembalian saldo tidak ditemukan',
                    'data' => null
                ],404);
            }

            if($pengembalian_saldo->status == 'berhasil'){
                return response()->json([
                    'message' => 'data pengembalian saldo sudah berhasil',
                    'data' => $pengembalian_saldo
                ],400);
            }

            $pengembalian_saldo->status = 'berhasil';
            $pengembalian_saldo->tanggal_proses = Carbon::now();
            $pengembalian_saldo->save();

            return response()->json([
                'message' => 'success menerima request pengembalian saldo',
                'data' => $pengembalian_saldo
            ]);

        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
    }
}
