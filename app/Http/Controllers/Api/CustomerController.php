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
                'email' => 'required|unique:karyawans,email|unique:customers,email|email',
                'no_telp' => 'required|unique:karyawans,no_telp|unique:customers,no_telp|digits_between:1,15|starts_with:08',
                'password' => 'required',
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
                'tanggal_lahir.before' => 'Maaf kamu belum cukup dewasa untuk mengakses web ini.',
                'email.email' => 'Email tidak valid.',
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
                    'email',
                    'unique:karyawans,email',
                    Rule::unique('customers')->ignore(Auth::id()),
                ],
                'no_telp' => [
                    'required',
                    'unique:karyawans,no_telp',
                    'digits_between:1,15',
                    'starts_with:08',
                    Rule::unique('customers')->ignore(Auth::id()),
                ],
                'foto_profile' => 'image:jpeg,png,jpg,gif,svg|max:4096',
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
                'tanggal_lahir.before' => 'Maaf kamu belum cukup dewasa untuk mengakses web ini.',
                'email.email' => 'Email tidak valid.',
                'foto_profile.image' => 'Foto harus berupa file gambar.',
                'foto_profile.max' => 'Ukuran foto terlalu besar, maksimal 4MB.',
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

            // jika ada request image
            if ($request->file('foto_profile')) {
                $uploadFolder = '/customer';

                $fileImage = $customer['foto_profile'];
                $imageUploadedPath = $fileImage->store($uploadFolder, 'public');

                // ambil url image yang disimpan di storage link
                // lalu masukkan ke db
                // $imageURL = Storage::url($imageUploadedPath);
                $customer['foto_profile'] = $imageUploadedPath;
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

    public function indexPesanan(Request $request)
    {
        try {
            $transaksi = Transaksi::query()
                ->whereHas('cart.customer', function ($query) {
                    $query->where('customer_id', Auth::id());
                })
                ->with(['statusTransaksi', 'alamat', 'cart.detailCart.produk', 'cart.detailCart.hampers', 'cart.customer']);

            if ($request->search) {
                $transaksi->where(function ($query) use ($request) {
                    $query->whereHas('cart.detailCart.produk', function ($query) use ($request) {
                        $query->where('nama_produk', 'like', '%' . $request->search . '%');
                    })
                        ->orWhereHas('cart.detailCart.hampers', function ($query) use ($request) {
                            $query->where('nama_hampers', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('statusTransaksi', function ($query) use ($request) {
                            $query->where('nama_status', 'like', '%' . $request->search . '%');
                        });
                });
            }

            if ($request->tanggal) {
                $transaksi->where('tanggal_pesan', 'like', '%' . $request->tanggal . '%');
            }

            if ($request->status) {
                if ($request->status != 13) {
                    $transaksi->where(function ($query) use ($request) {
                        $query->WhereHas('statusTransaksi', function ($query) use ($request) {
                            $query->where('id', 'like', '%' . $request->status . '%');
                        });
                    });
                }
            }

            $sortBy = 'created_at';

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $transaksi = $transaksi->orderBy($sortBy, $sortOrder)->get();

            return response()->json(
                [
                    'data' => $transaksi,
                    'message' => 'Berhasil mengambil data transaksi.'
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

    public function showPesanan(String $id)
    {
        try {
            $transaksi = Transaksi::query()
                ->where('id', $id)
                ->whereHas('cart.customer', function ($query) {
                    $query->where('customer_id', Auth::id());
                })
                ->with(['statusTransaksi', 'alamat', 'cart.detailCart.produk', 'cart.detailCart.hampers', 'cart.customer'])->get()->first();

            return response()->json(
                [
                    'data' => $transaksi,
                    'message' => 'Berhasil show data transaksi.'
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
