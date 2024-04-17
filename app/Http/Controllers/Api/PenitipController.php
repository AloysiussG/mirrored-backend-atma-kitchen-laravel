<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penitip;

class PenitipController extends Controller
{
    //CRUDS biasa untuk penitip, terdiri dari index, show, store, update, destroy
    public function index() {
        $penitips = Penitip::all();

        if($penitips){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $penitips
            ],200);
        }

        return reponse([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id) {
        $penitip = Penitip::find($id);

        if($penitip){
            return response([
                'message' => 'Retrieve Success',
                'data' => $penitip
            ],200);
        }

        return response([
            'message' => 'ID Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request) {
        $penitip = new Penitip;

        $penitip->nama_penitip = $request->nama_penitip;
        $penitip->alamat = $request->alamat;
        $penitip->no_hp = $request->no_hp;

        $penitip->save();

        return response([
            'message' => 'Add Penitip Success',
            'data' => $penitip
        ],200);
    }

    public function update(Request $request, $id) {
        $penitip = Penitip::find($id);

        if($penitip){
            $penitip->nama_penitip = $request->nama_penitip;
            $penitip->alamat = $request->alamat;
            $penitip->no_hp = $request->no_hp;

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
    }

    public function destroy($id) {
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
    }
}
