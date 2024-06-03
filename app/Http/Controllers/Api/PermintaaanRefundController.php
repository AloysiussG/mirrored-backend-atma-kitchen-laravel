<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PermintaanRefund;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\Validator;

class PermintaaanRefundController extends Controller
{
    public function indexByCustomer(){
        try{
            $pengembalian_saldo = PermintaanRefund::where('customer_id', Auth::id())->get();
            return response()->json([
                'message' => 'success mengambil data pengembalian saldo customer id '.Auth::id(),
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
            $pengembalian_saldo = PermintaanRefund::where('status', 'pending')->with('customer')->get();
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
            $customer = Customer::find(Auth::id());

            if(!$customer){
                return response()->json([
                    'message' => 'customer tidak ditemukan',
                    'data' => null
                ],404);
            }

           $validate = Validator::make($request->all(), [
                'nominal' => 'required | max:'.$customer->saldo. '| min: 1',
            ],
            [
                'nominal.required' => 'nominal harus diisi',
                'nominal.max' => 'nominal tidak boleh melebihi saldo'
            ]
        );

            if($validate->fails()){
                return response()->json([
                    'message' => $validate->errors(),
                    'data' => null
                ],400);
            }

            $pengembalian_saldo->customer_id = Auth::id();
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
