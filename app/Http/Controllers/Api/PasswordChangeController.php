<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordChanges;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\verifyPassChangeMail;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PasswordChangeController extends Controller
{
    public function store(Request $request)
    {
        try{
            // validasi request
            $validate = validator::make($request->all(), [
                'oldPass' => 'required',
                'newPass' => 'required',
            ]);

            if($validate->fails()){
                return response()->json([
                    'message' => $validate->errors(),
                ],400);
            }

            //simpan data request change pass
            $passwordChange = new passwordChanges();
            $passwordChange->customer_id = Customer::find(Auth::id())->id;
            //cek apakah user ada atau tidak, klo gada ya otomatis error somting wong
            if($passwordChange->customer_id == null){
                return response()->json([
                    'message' => 'User not found',
                ],404);
            }

            //cek password lama sama kek password akun ato ndak
            if(!Hash::check($request->oldPass, Customer::find(Auth::id())->password)){
                return response()->json([
                    'message' => 'Old password is incorrect',
                ],404);
            }
            //cek password lama sama ato ngga dengan password baru
            if(Hash::check($request->newPass, Customer::find(Auth::id())->password)){
                return response()->json([
                    'message' => 'New password cannot be the same as the old password',
                ],404);
            }

            $passwordChange->status = 'Not Verified';
            $passwordChange->oldPass = $request->oldPass;
            $passwordChange->newPass = $request->newPass;
            //verify code
            $passwordChange->verifyID = Str::random(8);;
            $passwordChange->save();
            //detail email
            $domain = URL::to('/');
            $detailEmail = [
                'name' => Customer::find(Auth::id())->nama,
                'link' =>  $domain . '/api/password-change/verify/'.$passwordChange->verifyID,

            ];
            //kirim email
            mail::to(Customer::find(Auth::id())->email)->send(new verifyPassChangeMail($detailEmail));
            //response json
            return response()->json([
                'message' => 'Password change request submitted successfully',
            ],200);
            }catch(Throwable $e){
                return response()->json([
                    'message' => $e->getMessage(),
                ],500);
            }
    }

    public function forgotPass(Request $request){
        try{
            // validasi request
            $validate = validator::make($request->all(), [
                'email' => 'required|email',
                'newPass' => 'required',
            ],[
                'email.required' => 'Email is required',
                'email.email' => 'Email is not valid',
                'newPass.required' => 'New password is required',
            ]);

            if($validate->fails()){
                return response()->json([
                    'message' => $validate->errors(),
                ],400);
            }

            //cari user
            $user = Customer::where('email', $request->email)->first();
            if($user == null){
                return response()->json([
                    'message' => 'Email is not registered',
                ],404);
            }

            //simpan data request change pass
            $passwordChange = new passwordChanges();
            $passwordChange->customer_id = $user->id;
            $passwordChange->oldPass = 'tidak teringat';
            $passwordChange->newPass = $request->newPass;
            $passwordChange->status = 'Not Verified';
            //verify code
            $passwordChange->verifyID = Str::random(8);;
            $passwordChange->save();
            //detail email
            $domain = URL::to('/');
            $detailEmail = [
                'name' => $user->nama,
                'link' =>  $domain . '/api/password-change/verify/'.$passwordChange->verifyID,

            ];
            //kirim email
            mail::to($user->email)->send(new verifyPassChangeMail($detailEmail));
            //response json
            return response()->json([
                'message' => 'Password change request submitted successfully',
            ],200);
            }catch(Throwable $e){
                return response()->json([
                    'message' => $e->getMessage(),
                ],500);
            }
    }

    public function verify($verifyID){

        try{
         //cari data password change request
        $verifying = passwordChanges::where('verifyID', $verifyID)->first();

        if($verifying == null){
            return response()->json([
                'message' => 'Password change request not found',
            ],404);
        }

        //cari user yang lagi verifying
        $user = Customer::findOrFail($verifying->customer_id);

        //cek apakah password change request sudah di verify atau belum
        if($verifying->verified_at != null){
            return response()->json([
                'message' => 'Password change request already verified',
            ],404);
        }

        //simpen data verified at, ganti password pengguna, update status password change request
        $verifying->verified_at = now();
        $user->password = Hash::make($verifying->newPass);
        $verifying->status = 'Verified';
        $verifying->save();
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully',
        ],200);
        }catch(Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
            ],500);
        }
    }
}
