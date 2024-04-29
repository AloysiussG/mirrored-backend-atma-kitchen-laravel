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
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $customerQuery = Customer::query()->with(['alamat']);
            if ($request->search) {
                $customerQuery->where('nama', 'like', '%' . $request->search . '%')->OrWhere('email', 'like', '%' . $request->search . '%')->OrWhere('no_telp', 'like', '%' . $request->search . '%');
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama', 'email', 'no_telp'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $customers = $customerQuery->orderBy($sortBy, $sortOrder)->get();
            return response([
                'message' => 'Retrieve All Success',
                'data' => $customers
            ], 200);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
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
                'email' => 'required|unique:karyawans,email|unique:customers,email',
                'no_telp' => 'required|unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15|starts_with:08',
                'tanggal_lahir' => 'required|before:2008-01-01'
            ], [
                'nama.required' => 'Nama tidak boleh kosong.',
                'password.required' => 'Password Tidak Boleh Kosong',
                'email.required' => 'Email Tidak Boleh Kosong',
                'no_telp.required' => 'Nomor Telepon Tidak Boleh Kosong',
                'tanggal_lahir.required' => 'Tanggal Lahir Tidak Boleh Kosong',
                'email.unique' => 'Email sudah terdaftar.',
                'no_telp.unique' => 'Nomor telepon sudah terdaftar.',
                'no_telp.digits_between' => 'Nomor telepon tidak valid',
                'no_telp.starts_with' => 'Nomor telepon harus diawali dengan 08.',
                'tanggal_lahir.date' => 'Kolom tanggal lahir harus berupa tanggal.',
                'tanggal_lahir.before' => 'Maaf kamu belum cukup dewasa untuk mengakses web ini.'
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
            $customer['saldo'] = 0;
            $customer['poin'] = 0;
            $customer = Customer::create($customer);
            //response json
            return response()->json(
                [
                    'data' => $customer,
                    'message' => 'Register Berhasil.',
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
                    'message' => 'Berhasil mengambil 1 data Customer.',
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
                'nama' => 'required',
                'tanggal_lahir' => 'required|before:2008-01-01',
                'email' => [
                    'required',
                    Rule::unique('customers')->ignore(Auth::id()), 
                ],
                'no_telp' => [
                    'required',
                    'unique:karyawans,no_telp',
                    'digits_between:1,15',
                    'starts_with:08',
                    'unique:karyawans,email',
                    Rule::unique('customers')->ignore(Auth::id()), 
                ],
            ], [
                'nama.required' => 'Nama tidak boleh kosong.',
                'email.required' => 'Email Tidak Boleh Kosong',
                'no_telp.required' => 'Nomor Telepon Tidak Boleh Kosong',
                'tanggal_lahir.required' => 'Tanggal Lahir Tidak Boleh Kosong',
                'email.unique' => 'Email sudah terdaftar.',
                'no_telp.unique' => 'Nomor telepon sudah terdaftar.',
                'no_telp.digits_between' => 'Nomor telepon tidak valid',
                'no_telp.starts_with' => 'Nomor telepon harus diawali dengan 08.',
                'tanggal_lahir.date' => 'Kolom tanggal lahir harus berupa tanggal.',
                'tanggal_lahir.before' => 'Maaf kamu belum cukup dewasa untuk mengakses web ini.'
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

            $customerUpdate->update($customer);
            return response()->json(
                [
                    'data' => $customerUpdate,
                    'message' => 'Berhasil mengubah data Customer.',
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
