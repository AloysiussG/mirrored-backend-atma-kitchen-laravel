<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Validator;
use App\Models\Penggajian;
use App\Models\Karyawan;

class PenggajianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $penggajian = Penggajian::query()->with('karyawan');

            if ($request->search) {
                $penggajian
                    ->where('karyawan_id', 'like', '%' . $request->search . '%')
                    ->orWhere('total_gaji', 'like', '%' . $request->search . '%')
                    ->orWhere('tanggal_gaji', 'like', '%' . $request->search . '%');
            }

            if ($request->karyawan) {
                $penggajian->whereHas('karyawan', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'karyawan_id',
                'total_gaji',
                'tanggal_gaji',
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

            $penggajianData = $penggajian->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $penggajianData,
                    'message' => 'Berhasil mengambil data penggajian.'
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
        try {
            $penggajian = $request->all();
            $validate = Validator::make($penggajian, [
                'karyawan_id' => 'required',
                'total_gaji' => 'required',
                'tanggal_gaji' => 'required|date|before:tomorrow',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data Penggajian tidak valid.',
                    ],
                    400
                );
            }

            $penggajian = Penggajian::create($penggajian);
            return response()->json(
                [
                    'data' => $penggajian,
                    'message' => 'Berhasil membuat data penggajian baru.',
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $penggajian = Penggajian::with('karyawan')->find($id);
            if (!$penggajian) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Penggajian tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $penggajian,
                    'message' => 'Berhasil mengambil 1 data Penggajian.',
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $penggajianUpdate = Penggajian::find($id);
            if (!$penggajianUpdate) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Penggajian tidak ditemukan.',
                    ],
                    404
                );
            }
            
            if ($request['karyawan_id']) {
                $karyawan = Karyawan::find($request['karyawan_id']);
                if (!$karyawan) {
                    return response()->json(
                        [
                            'data' => null,
                            'message' => 'Karyawan tidak ditemukan.',
                        ],
                        404
                    );
                }
            }

            //validator
            $penggajian = $request->all();
            $validate = Validator::make($penggajian, [
                'tanggal_gaji' => 'date|before:tomorrow',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data Penggajian tidak valid',
                    ],
                    400
                );
            }

            $penggajianUpdate->update($penggajian);
            return response()->json(
                [
                    'data' => $penggajianUpdate,
                    'message' => 'Berhasil mengubah data penggajian.',
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $penggajian = Penggajian::find($id);
            if (!$penggajian) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Penggajian tidak ditemukan.',
                    ],
                    404
                );
            }

            if (!$penggajian->delete()) {
                return response()->json(
                    [
                        'data' => $penggajian,
                        'message' => 'Gagal menghapus data penggajian.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $penggajian,
                    'message' => 'Berhasil menghapus data penggajian.',
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
