<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use Throwable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public function index(Request $request){
        try{
            $pengeluarans = Pengeluaran::query();
            if ($request->search) {
                $pengeluarans->where('jenis_pengeluaran', 'like', '%' . $request->search . '%')->orWhereMonth('tanggal_pengeluaran','=', $request->search)->orWhereDate('tanggal_pengeluaran','=', $request->search);
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'jenis_pengeluaran','total_pengeluaran','created_at'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $pengeluaranHasil = $pengeluarans->orderBy($sortBy, $sortOrder)->get();

            if($pengeluaranHasil){
                return response([
                    'message' => 'Retrieve Success',
                    'data' => $pengeluaranHasil
                ],200);
            }

        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function show($id) {
        try{
            $pengeluaran = Pengeluaran::find($id);

            if($pengeluaran){
                return response([
                    'message' => 'Retrieve Success',
                    'data' => $pengeluaran
                ],200);
            }
        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function store(Request $request) {
        try{

            $validate = Validator::make($request->all(), [
                'jenis_pengeluaran' => 'required',
                'total_pengeluaran' => 'required|numeric',
                'tanggal_pengeluaran' => 'required|date|date_format:Y-m-d'
            ]);

            if($validate->fails()){
                return response([
                    'message' => $validate->errors(),
                    'data' => null
                ],400);
            }


            $pengeluaran = new Pengeluaran;

            $pengeluaran->jenis_pengeluaran = $request->jenis_pengeluaran;
            $pengeluaran->total_pengeluaran = $request->total_pengeluaran;
            $pengeluaran->tanggal_pengeluaran = $request->tanggal_pengeluaran;

            $pengeluaran->save();

            return response([
                'message' => 'Create Success',
                'data' => $pengeluaran
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
            $pengeluaran = Pengeluaran::find($id);

            if($pengeluaran){

                $validate = Validator::make($request->all(), [
                    'jenis_pengeluaran' => 'required',
                    'total_pengeluaran' => 'required|numeric',
                    'tanggal_pengeluaran' => 'required|date'
                ]);

                $pengeluaran->jenis_pengeluaran = $request->jenis_pengeluaran;
                $pengeluaran->total_pengeluaran = $request->total_pengeluaran;
                $pengeluaran->tanggal_pengeluaran = $request->tanggal_pengeluaran;

                $pengeluaran->save();
                return response([
                    'message' => 'Update Success',
                    'data' => $pengeluaran
                ],200);

            }
        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function destroy($id) {
        try{
            $pengeluaran = Pengeluaran::find($id);

            if($pengeluaran){
                $pengeluaran->delete();
                return response([
                    'message' => 'Delete Success',
                    'data' => $pengeluaran
                ],200);
            }
        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }

    public function totalPengeluaran(){
        try{
            $totalPengeluaran = DB::table('pengeluarans')->sum('total_pengeluaran');

            if($totalPengeluaran){
                return response([
                    'message' => 'Retrieve Success',
                    'data' => $totalPengeluaran
                ],200);
            }

        }catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],500);
        }
    }


}
