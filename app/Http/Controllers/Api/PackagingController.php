<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Packaging;
use Illuminate\Http\Request;
use Throwable;

class PackagingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexByItemId(Request $request)
    {
        try {
            $packagingQuery = Packaging::query()->with('bahanBaku');

            if ($request->produk) {
                $packagingQuery->where('produk_id', $request->produk);
            }

            if ($request->hampers) {
                $packagingQuery->where('hampers_id', $request->hampers);
            }

            if ($request->transaksi) {
                $packagingQuery->where('transaksi_id', $request->transaksi);
            }

            $packaging = $packagingQuery->get();

            return response()->json(
                [
                    'data' => $packaging,
                    'message' => 'Berhasil mengambil data packaging.'
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
