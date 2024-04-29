<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    public function getUserDataByToken(Request $request)
    {
        try {
            $userDataByToken = $request->user();

            // cek di Customer
            $userData = Customer::query()
                ->where('email', '=', $userDataByToken['email'])
                ->first();

            // jika tidak ketemu, cek di Karyawan
            if (!$userData) {
                $userData = Karyawan::query()
                    ->where('email', '=', $userDataByToken['email'])
                    ->with('role')
                    ->first();
            }

            // tambah role 'Customer' ke Customer untuk bedain Customer & Guest
            // Karyawan sudah punya role
            if ($userData && !$userData['role']) {
                $userData['role'] = [
                    'role_name' => 'Customer',
                ];
            }

            return response()->json(
                [
                    'data' => $userData,
                    'message' => 'Berhasil mengambil data user.'
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

    public function loginByEmail(Request $request)
    {
        try {
            $userDataRequest = $request->all();

            $validate = Validator::make($userDataRequest, [
                'email' => 'required|email:rfc,dns',
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
                ->first();

            // jika tidak ketemu, cek di Karyawan
            if (!$userData) {
                $userData = Karyawan::query()
                    ->where('email', '=', $userDataRequest['email'])
                    ->with('role')
                    ->first();
            }

            // jika masih tidak ketemu, throw invalid response
            if (!$userData) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'User tidak ditemukan.',
                    ],
                    404
                );
            }

            // jika user ditemukan tapi email/password salah
            if (!Hash::check($userDataRequest['password'], $userData->password)) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Email atau password salah.',
                    ],
                    404
                );
            }

            // buat token ability berdasarkan role name
            $roleName = 'customer';
            if ($userData->role?->role_name) {
                $roleName = Str::of($userData->role->role_name)->slug('-');
            }

            // jika ditemukan dan password benar, authorize dengan token juga 
            $userToken = $userData->createToken('Login Token', [$roleName])->plainTextToken;

            // tambah role 'Customer' ke Customer untuk bedain Customer & Guest
            // Karyawan sudah punya role
            if ($userData && !$userData['role']) {
                $userData['role'] = [
                    'role_name' => 'Customer',
                ];
            }

            return response()->json(
                [
                    'data' => $userData,
                    'token' => $userToken,
                    'message' => 'Login berhasil.',
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

    public function logout(Request $request)
    {
        try {
            // hapus current user token
            $request->user()->currentAccessToken()->delete();
            return response()->json(
                [
                    'data' => null,
                    'message' => 'Logout berhasil.',
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
