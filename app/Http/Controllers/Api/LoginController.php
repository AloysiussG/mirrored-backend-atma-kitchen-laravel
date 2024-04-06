<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LoginController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => null,
            'message' => 'Berhasil.'
        ], 200);
    }

    public function loginByEmail(Request $request)
    {
        try {
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
                // [TODO] 
                // ini belum dihash, nanti pakai Hash::make
                ->where('password', '=', $userDataRequest['password'])
                ->first();

            // jika tidak ketemu, cek di Karyawan
            if (!$userData) {
                $userData = Karyawan::query()
                    ->where('email', '=', $userDataRequest['email'])
                    // [TODO] 
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
            $userToken = $userData->createToken('Login Token')->plainTextToken;
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
