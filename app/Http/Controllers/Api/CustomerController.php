<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\verifyRegisterMail;
use App\Models\Cart;
use App\Models\DetailCart;
use App\Models\Produk;
use App\Models\Transaksi;
use Throwable;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
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
    public function store(Request $request)
    {
        try {
            // validasi request
            $customer = $request->all();
            $validate = Validator::make($customer, [
                'nama' => 'required',
                'password' => 'required',
                'email' => 'unique:karyawans,email|unique:customers,email',
                'no_telp' => 'unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15',
                'tanggal_lahir' => 'date|before:2008-01-01'
            ]);
            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data customer tidak valid.',
                    ],
                    400
                );
            }
            $customer['saldo'] = 0;
            $customer['poin'] = 0;
            $customer['status'] = 'Not Verified';
            //verify code
            $customer['verifyID'] = Str::random(8);
            $customer = Customer::create($customer);
            //detail email
            $domain = URL::to('/');
            $detailEmail = [
                'name' => $customer['nama'],
                'link' =>  $domain . '/api/customer/verify/' . $customer['verifyID'],
            ];
            //kirim email
            mail::to($customer['email'])->send(new VerifyRegisterMail($detailEmail));
            //response json
            return response()->json(
                [
                    'message' => 'Email verification request submitted successfully',
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

    public function verify($verifyID)
    {
        //cari data password change request
        $customer = Customer::where('verifyID', $verifyID)->first();

        if ($customer == null) {
            return response()->json(
                [
                    'message' => 'Email verification request not found',
                ],
                404
            );
        }

        //cek apakah password change request sudah di verify atau belum
        if ($customer->verified_at != null) {
            return response()->json([
                'message' => 'Email verification request already verified',
            ], 404);
        }

        //simpen data verified at
        $customer->verified_at = now();
        $customer->status = 'Verified';
        $customer->save();

        return response()->json(
            [
                'message' => 'Email verification is done successfully',
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        try {
            $customer = Customer::find(Auth::id());
            if (!$customer) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Customer tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $customer,
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
    public function update(Request $request)
    {
        try {
            $customerUpdate = Customer::find(Auth::id());
            if (!$customerUpdate) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Customer tidak ditemukan.',
                    ],
                    404
                );
            }

            $customer = $request->all();
            $validate = Validator::make($customer, [
                'email' => 'unique:karyawans,email|unique:customers,email',
                'no_telp' => 'unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15',
                'tanggal_lahir' => 'date|before:2007-01-01'
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Data customer tidak valid',
                    ],
                    400
                );
            }

            $customerUpdate->update($customer);
            return response()->json(
                [
                    'data' => $customerUpdate,
                    'message' => 'Berhasil mengubah data karyawan.',
                ],
                200
            );
        } catch (Throwable $th) {
        }
    }

    public function showHistory()
    {
        try {
            //get all current customer carts 
            $cart = Cart::where('customer_id', Auth::id())->get();
            $history = [];

            //get each transaksi with customer cart_id
            foreach ($cart as $cartItem) {
                $transaksi = Transaksi::with('statusTransaksi')->where('cart_id', $cartItem->id)->get();
                if ($transaksi->isNotEmpty()) {
                    $history = array_merge($history, $transaksi->toArray());
                }
            }
            //if history is null or not found
            if (!$history) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'History Transaksi tidak ditemukan.',
                    ],
                    404
                );
            }
            return response()->json(
                [
                    'data' => $history,
                    'message' => 'Berhasil mengambil data History Transaksi.',
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

    //find history by nama produk
    public function searchHistory(Request $request)
    {
        try {
            $namaProduk = $request['nama_produk'];
            //get all current customer carts
            $cart = Cart::where('customer_id', Auth::id())->get();
            //get id produk based on nama
            $produk = Produk::where('nama_produk', $namaProduk)->first();
            if (!$produk) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Nama Produk tidak ditemukan.',
                    ],
                    404
                );
            }
            $detailCartAll = [];
            $history = [];
            //find all detailCart with idProduk and idCart

            foreach ($cart as $i) {
                $detailCart = DetailCart::with('produk')
                    ->where('produk_id', $produk->id)
                    ->where('cart_id', $i->id)->get();
                if ($detailCart->isNotEmpty()) {
                    $detailCartAll = array_merge($detailCartAll, $detailCart->toArray());
                }
            }
            if (!$detailCartAll) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Customer belum membeli produk ini.',
                    ],
                    404
                );
            }

            //get each transaksi with customer cart_id
            foreach ($detailCartAll as $i) {
                $transaksi = Transaksi::where('cart_id', $i['cart_id'])->get();
                foreach ($transaksi as $t) {
                    $t['nama_produk'] = $i['produk']['nama_produk'];
                }
                if ($transaksi->isNotEmpty()) {
                    $history = array_merge($history, $transaksi->toArray());
                }
            }
            //if history is null or not found
            if (!$history) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'History Transaksi tidak ditemukan.',
                    ],
                    404
                );
            }

            return response()->json(
                [
                    'data' => $history,
                    'message' => 'Berhasil mengambil data History Transaksi.',
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
