<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function loginByEmail(Request $request)
    {
        $userDataRequest = $request->all();

        $validate = Validator::make($userDataRequest, [
            // 'email' => 'required|email:rfc,dns',
            'email' => 'required|email:rfc',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(
                [
                    'data' => null,
                    'message' => 'Login request tidak valid.',
                ],
                400
            );
        }

        // cek di Customer
        $userData = Customer::query()
            ->where('email', '=', $userDataRequest['email'])
            // [COMING SOON] ini belum dihash, nanti pakai Hash::make
            ->where('password', '=', $userDataRequest['password'])
            ->first();

        // jika tidak ketemu, cek di Karyawan
        if (!$userData) {
            $userData = Karyawan::query()
                ->where('email', '=', $userDataRequest['email'])
                // ini belum dihash, nanti pakai Hash::make
                ->where('password', '=', $userDataRequest['password'])
                ->with('role')
                ->first();
        }

        // jika masih tidak ketemu, throw invalid response
        if (!$userData) {
            return response()->json(
                [
                    'data' => null,
                    'message' => 'Email atau password salah.',
                ],
                404
            );
        }

        // jika ditemukan, authorize dengan token juga
        // [COMING SOON] tambah token Laravel Sanctum
        return response()->json(
            [
                'data' => $userData,
                'token' => null,
                'message' => 'Login berhasil.',
            ],
            200
        );
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
