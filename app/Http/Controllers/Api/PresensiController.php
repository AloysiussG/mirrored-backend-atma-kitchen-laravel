<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\Presensi;
use Throwable;
use Illuminate\Support\Facades\Validator;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $presensi = Presensi::query()->with('karyawan');

            if ($request->search) {
                $presensi
                    ->where('karyawan_id', 'like', '%' . $request->search . '%')
                    ->orWhere('tanggal_bolos', 'like', '%' . $request->search . '%');
            }

            if ($request->karyawan) {
                $presensi->whereHas('karyawan', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'karyawan_id',
                'created_at'
            ])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $presensiData = $presensi->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $presensiData,
                    'message' => 'Berhasil mengambil data presensi.'
                ],
                200
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage()
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
        try{
            $karyawan = Karyawan::find($request['karyawan_id']);
            if(!$karyawan){
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }
            $presensi = $request->all();
            $validate = Validator::make($presensi, [
                'karyawan_id' => 'required',
                'tanggal_bolos' => 'required|before:tomorrow',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data Presensi tidak valid.',
                    ],
                    400
                );
            }

            $presensi = Presensi::create($presensi);
            return response()->json(
                [
                    'data' => $presensi,
                    'message' => 'Berhasil membuat data presensi baru.',
                ],
                200
            );
        }catch (Throwable $th){
            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $presensi = Presensi::with('karyawan')->find($id);
            if (!$presensi) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Presensi tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $presensi,
                    'message' => 'Berhasil mengambil 1 data Presensi.',
                ],
                200
            );
        }catch (Throwable $th){
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $presensiUpdate = Presensi::find($id);
            if (!$presensiUpdate) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Presensi tidak ditemukan.',
                    ],
                    404
                );
            }

            $karyawan = Karyawan::find($request['karyawan_id']);
            $presensi = $request->all();
            if(!$karyawan){
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }
            //validator
            $validate = Validator::make($presensi, [
                'tanggal_bolos' => 'date|before:tomorrow',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data presensi tidak valid',
                    ],
                    400
                );
            }

            $presensiUpdate->update($presensi);
            return response()->json(
                [
                    'data' => $presensiUpdate,
                    'message' => 'Berhasil mengubah data presensi.',
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
}
