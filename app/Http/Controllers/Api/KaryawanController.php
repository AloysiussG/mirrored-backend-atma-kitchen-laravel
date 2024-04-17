<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Support\Carbon;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $karyawan = Karyawan::query()->with('role');

            if ($request->search) {
                $karyawan
                    ->whereHas('role', function ($query) use ($request) {
                        $query->where('nama', 'like', '%' . $request->search . '%');
                    })
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('no_telp', 'like', '%' . $request->search . '%')
                    ->orWhere('hire_date', 'like', '%' . $request->search . '%')
                    ->orWhere('gaji', 'like', '%' . $request->search . '%')
                    ->orWhere('bonus_gaji', 'like', '%' . $request->search . '%');
            }

            if ($request->role) {
                $karyawan->whereHas('role', function ($query) use ($request) {
                    $query->where('role_name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->sortBy && in_array($request->sortBy, [
                'id',
                'role_id',
                'nama',
                'hire_date',
                'gaji',
                'bonus_gaji',
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

            $karyawanData = $karyawan->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $karyawanData,
                    'message' => 'Berhasil mengambil data karyawan.'
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
            $karyawan = $request->all();
            //hire date == tanggal karyawan dibuat
            $karyawan['hire_date'] = Carbon::now();

            //validator
            $validate = Validator::make($karyawan, [
                'role_id' => 'required',
                'nama' => 'required',
                'password' => 'required',
                'email' => 'required|unique:karyawans,email|unique:customers,email',
                'no_telp' => 'required|unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15',
                'gaji' => 'required',
                'bonus_gaji' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data karyawan tidak valid.',
                    ],
                    400
                );
            }

            $newKaryawan = Karyawan::create($karyawan);
            return response()->json(
                [
                    'data' => $newKaryawan,
                    'message' => 'Berhasil membuat data karyawan baru.',
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $karyawan = Karyawan::with('role')->find($id);

            if (!$karyawan) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $karyawan,
                    'message' => 'Berhasil mengambil 1 data Karyawan.',
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
            $karyawanUpdate = Karyawan::find($id);

            if (!$karyawanUpdate) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }

            $karyawan = $request->all();

            //validator
            $validate = Validator::make($karyawan, [
                'email' => 'unique:karyawans,email|unique:customers,email',
                'no_telp' => 'unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data karyawan tidak valid',
                    ],
                    400
                );
            }

            $karyawanUpdate->update($karyawan);
            return response()->json(
                [
                    'data' => $karyawanUpdate,
                    'message' => 'Berhasil mengubah data karyawan.',
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
            $karyawan = Karyawan::find($id);
            if (!$karyawan) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karywan tidak ditemukan.',
                    ],
                    404
                );
            }

            if (!$karyawan->delete()) {
                return response()->json(
                    [
                        'data' => $karyawan,
                        'message' => 'Gagal menghapus data karyawan.',
                    ],
                    500
                );
            }

            return response()->json(
                [
                    'data' => $karyawan,
                    'message' => 'Berhasil menghapus data karyawan.',
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

    //mengubah password biasa
    //hanya untuk karyawan
    public function changePassword(Request $request, $id)
    {
        try {
            //find karyawan yang sedang melakukan ganti password
            $karyawan = Karyawan::find($id);
            if (!$karyawan) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'User tidak ditemukan.',
                    ],
                    404
                );
            }

            //validator
            $validate = Validator::make($request->all(), [
                'password' => 'required',
            ]);
            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages(),
                    ],
                    400
                );
            }

            //update
            $karyawan->update($request->all());
            return response()->json(
                [
                    'data' => $karyawan,
                    'message' => 'Berhasil mengupdate password user.',
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
