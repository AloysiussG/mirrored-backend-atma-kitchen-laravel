<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penitip;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PenitipController extends Controller
{
    //CRUDS biasa untuk penitip, terdiri dari index, show, store, update, destroy
    public function index(Request $request) {
        try{
            $penitips = Penitip::query();
            if ($request->search) {
                $penitips->where('nama_penitip', 'like', '%' . $request->search . '%');
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_penitip', 'created_at'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $penitipHasil = $penitips->orderBy($sortBy, $sortOrder)->get();

            if($penitipHasil){
                return response([
                    'message' => 'Retrieve Success',
                    'data' => $penitipHasil
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
        $penitip = Penitip::find($id);

        if($penitip){
            return response([
                'message' => 'Retrieve Success',
                'data' => $penitip
            ],200);
        }
       }catch(Throwable $e){
        return response([
            'message' => $e->getMessage(),
            'data' => null
        ],404);
        }
    }

    public function store(Request $request) {
       try{
            $penitip = new Penitip;

            $penitip->nama_penitip = $request->nama_penitip;

            $penitip->save();

            return response([
                'message' => 'Add Penitip Success',
                'data' => $penitip
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
            $penitip = Penitip::find($id);

            if($penitip){

                $validate = Validator::make($request->all(), [
                    'nama_penitip' => 'required'
                ]);
                $penitip->nama_penitip = $request->nama_penitip;

                $penitip->save();

                return response([
                    'message' => 'Update Penitip Success',
                    'data' => $penitip
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
            $penitip = Penitip::find($id);

            if($penitip){
                $penitip->delete();

                return response([
                    'message' => 'Delete Penitip Success',
                    'data' => $penitip
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
