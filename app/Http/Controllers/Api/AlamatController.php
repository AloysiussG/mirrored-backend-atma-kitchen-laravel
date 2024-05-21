<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Throwable;

class AlamatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexMyAlamat(Request $request)
    {
        try {
            // --- CARI ACTIVE CART
            $user = $request->user();
            $alamats = Alamat::query()
                ->where('customer_id', $user->id)
                ->get();

            if (!$alamats) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Alamat tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $alamats,
                    'message' => 'Berhasil index alamat.',
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
