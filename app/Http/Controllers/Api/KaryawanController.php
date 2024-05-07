<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Penggajian;
use App\Models\Presensi;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
                    $query->where('role_name', 'like', '%' . $request->role . '%');
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

            //validator
            $validate = Validator::make($karyawan, [
                'role_id' => 'required',
                'nama' => 'required',
                'email' => 'required|unique:karyawans,email|unique:customers,email|email',
                'password' => 'required',
                'no_telp' => 'required|unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15|starts_with:08',
                'hire_date' => 'required|before:tomorrow',
            ], [
                'nama.required' => 'Nama tidak boleh kosong.',
                'password.required' => 'Password tidak boleh kosong.',
                'email.required' => 'Email Tidak Boleh Kosong',
                'no_telp.required' => 'Nomor Telepon Tidak Boleh Kosong',
                'role_id.required' => 'Role harus dipilih.',
                'email.unique' => 'Email sudah terdaftar.',
                'no_telp.unique' => 'Nomor telepon sudah terdaftar.',
                'no_telp.digits_between' => 'Nomor telepon tidak valid',
                'no_telp.starts_with' => 'Nomor telepon harus diawali dengan 08.',
                'email.email' => 'Email tidak valid.',
                'hire_date.required' => 'Tanggal masuk tidak boleh kosong.',
                'hire_date.before' => 'Tanggal masuk tidak valid.',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->errors()->first(),
                    ],
                    400
                );
            }

            if ($karyawan['role_id'] == 1) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Role tidak valid.',
                    ],
                    400
                );
            } else if ($karyawan['role_id'] == 2) {
                $karyawan['bonus_gaji'] = 250000;
                $karyawan['gaji'] = 4500000;
            } else if ($karyawan['role_id'] == 3) {
                $karyawan['bonus_gaji'] = 500000;
                $karyawan['gaji'] = 6000000;
            } else if ($karyawan['role_id'] == 4) {
                $karyawan['bonus_gaji'] = 100000;
                $karyawan['gaji'] = 3000000;
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
                'role_id' => 'required',
                'nama' => 'required',
                'hire_date' => 'required|before:tomorrow',
                'email' => [
                    'required',
                    'email',
                    'unique:customers,email',
                    Rule::unique('karyawans')->ignore($id),
                ],
                'no_telp' => [
                    'required',
                    'unique:customers,no_telp',
                    'digits_between:1,15',
                    'starts_with:08',
                    Rule::unique('karyawans')->ignore($id),
                ],
            ], [
                'nama.required' => 'Nama tidak boleh kosong.',
                'email.required' => 'Email Tidak Boleh Kosong',
                'no_telp.required' => 'Nomor Telepon Tidak Boleh Kosong',
                'role_id.required' => 'Role harus dipilih.',
                'email.unique' => 'Email sudah terdaftar.',
                'no_telp.unique' => 'Nomor telepon sudah terdaftar.',
                'no_telp.digits_between' => 'Nomor telepon tidak valid',
                'no_telp.starts_with' => 'Nomor telepon harus diawali dengan 08.',
                'email.email' => 'Email tidak valid.',
                'hire_date.required' => 'Tanggal masuk tidak boleh kosong.',
                'hire_date.before' => 'Tanggal masuk tidak valid.',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->errors()->first(),
                    ],
                    400
                );
            }

            if ($karyawan['role_id'] == 1) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Role tidak valid.',
                    ],
                    400
                );
            } else if ($karyawan['role_id'] == 2) {
                $karyawan['bonus_gaji'] = 250000;
                $karyawan['gaji'] = 4500000;
            } else if ($karyawan['role_id'] == 3) {
                $karyawan['bonus_gaji'] = 500000;
                $karyawan['gaji'] = 6000000;
            } else if ($karyawan['role_id'] == 4) {
                $karyawan['bonus_gaji'] = 100000;
                $karyawan['gaji'] = 3000000;
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
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }

            $penggajian = Penggajian::where('karyawan_id', $id)->get();
            if($penggajian){
                foreach($penggajian as $gaji){
                    $gaji->delete();
                }
            }

            $presensi = Presensi::where('karyawan_id', $id)->get();
            if($presensi){
                foreach($presensi as $presensi){
                    $presensi->delete();
                }
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
    public function changePassword(Request $request)
    {
        try {
            //find karyawan yang sedang melakukan ganti password
            $karyawan = Karyawan::find(Auth::id());
            if (!$karyawan) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }

            //validator
            $validate = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required',
                'new_password_confirm' => 'required|same:new_password',
            ], [
                'old_password.required' => 'Password lama tidak boleh kosong.',
                'new_password.required' => 'Password baru tidak boleh kosong.',
                'new_password_confirm.required' => 'Konfirmasi password tidak boleh kosong.',
                'new_password_confirm.same' => 'Konfirmasi password wajib sama.'
            ]);
            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->errors()->first(),
                    ],
                    400
                );
            }

            // password salah
            if (!Hash::check($request['old_password'], $karyawan->password)) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Password lama salah.',
                    ],
                    404
                );
            }

            //update
            $karyawan->update([
                'password' => $request['new_password']
            ]);
            return response()->json(
                [
                    'data' => $karyawan,
                    'message' => 'Berhasil mengupdate password.',
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

    //mengubah gaji
    //hanya untuk owner
    public function changeGaji(Request $request, $id)
    {
        try {
            //find karyawan
            $karyawan = Karyawan::find($id);
            if (!$karyawan) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Karyawan tidak ditemukan.',
                    ],
                    404
                );
            }

            //validator
            $validate = Validator::make($request->all(), [
                'bonus_gaji' => 'required|gt:0',
                'gaji' => 'required|gt:0',
            ], [
                'bonus_gaji.required' => 'Bonus gaji harus diisi.',
                'gaji.required' => 'Gaji harus diisi.',
                'bonus_gaji.gt' => 'Bonus gaji tidak boleh kurang dari 0.',
                'gaji.gt' => 'Gaji tidak boleh kurang dari 0.',
            ]);
            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages()->first(),
                    ],
                    400
                );
            }

            $karyawan->update($request->only(['bonus_gaji', 'gaji']));
            return response()->json(
                [
                    'data' => $karyawan,
                    'message' => 'Berhasil mengupdate gaji dan bonus gaji Karyawan.',
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
