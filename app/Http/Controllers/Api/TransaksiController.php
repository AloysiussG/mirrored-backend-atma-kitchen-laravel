<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Hampers;
use App\Models\Produk;
use App\Models\Resep;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use DateTime;
use Throwable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    public function findByCustomer(Request $request)
    {
        try {
            $transaksiQuery = Transaksi::query()->with(['cart.customer', 'statusTransaksi', 'packagings.bahanBaku', 'alamat']);
            if ($request->search) {
                $transaksiQuery->whereHas('cart.customer', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->search . '%');
                })->orwhereHas('statusTransaksi', function ($query) use ($request) {
                    $query->where('nama_status', 'like', '%' . $request->search . '%');
                })->orWhere('no_nota', 'like', '%' . $request->search . '%');
            }
            if ($request->date) {
                $transaksiQuery->whereDate('tanggal_pesan', $request->date);
            }

            if ($request->status) {
                $transaksiQuery->where('status_transaksi_id', $request->status);
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'total_harga', 'status', 'tanggal_pesan'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $transaksis = $transaksiQuery->orderBy($sortBy, $sortOrder)->get();

            //update status transaksi menjadi batal jika belum
            //kembalikan stok produk yang ready stock jika belum
            foreach ($transaksis as $transaksi) {
                //cek apakah setiap produk dalam transaksi ready stock
                $status_transaksi = "Ready Stock";
                foreach ($transaksi->cart->detailCart as $detailCart) {
                    if ($detailCart->status_produk == "Pre Order") {
                        $status_transaksi = "Pre Order";
                        break;
                    }
                }
                $tanggal_ambil = new DateTime($transaksi->tanggal_ambil);
                $tanggal_sekarang = new DateTime(Carbon::now()->addDay()->toDateString());
                if ($transaksi->status_transaksi_id != 12 && $transaksi->tanggal_lunas == null && $tanggal_ambil <= $tanggal_sekarang) {
                    //jika semua produk ready stock, tanggal ambil boleh = tanggal hari ini
                    //jadi transaksi yang semua produknya ready stock dan taggal ambil = tanggal hari ini bakal di remove
                    if ($status_transaksi == "Ready Stock") {
                        $tanggal_sekarang = new DateTime(Carbon::now()->toDateString());
                        if ($tanggal_ambil < $tanggal_sekarang) {
                            foreach ($transaksi->cart->detailCart as $detailCart) {
                                if ($detailCart->produk_id != null) {
                                    $produk = $detailCart->produk;
                                    $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                                    $produk->status = "Ready Stock";
                                    $produk->save();
                                } else {
                                    $hampers = $detailCart->hampers;
                                    foreach ($hampers->detailHampers as $detailHampers) {
                                        for ($i = 0; $i < $detailCart->jumlah; $i++) {
                                            $produk = $detailHampers->produk;
                                            $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                            $produk->status = "Ready Stock";
                                            $produk->save();
                                        }
                                    }
                                }
                            }
                            $customer = $transaksi->cart->customer;
                            $customer->poin = $customer->poin + $transaksi->poin_dipakai;
                            $customer->save();
                            $transaksi->status_transaksi_id = 12;
                            $transaksi->save();
                            $title = 'Status Transaksi Diperbaharui';
                            $body = $transaksi->statusTransaksi->nama_status;
                            $customer->notify(new PushNotification($title, $body));
                        }
                    } else if ($status_transaksi == "Pre Order") {
                        foreach ($transaksi->cart->detailCart as $detailCart) {
                            if ($detailCart->status_produk == "Ready Stock") {
                                if ($detailCart->produk_id != null) {
                                    $produk = $detailCart->produk;
                                    $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                                    $produk->status = "Ready Stock";
                                    $produk->save();
                                } else {
                                    $hampers = $detailCart->hampers;
                                    foreach ($hampers->detailHampers as $detailHampers) {
                                        for ($i = 0; $i < $detailCart->jumlah; $i++) {
                                            $produk = $detailHampers->produk;
                                            $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                            $produk->status = "Ready Stock";
                                            $produk->save();
                                        }
                                    }
                                }
                            }
                        }
                        $customer = $transaksi->cart->customer;
                        $customer->poin = $customer->poin + $transaksi->poin_dipakai;
                        $customer->save();
                        $transaksi->status_transaksi_id = 12;
                        $transaksi->save();
                        $title = 'Status Transaksi Diperbaharui';
                        $body = $transaksi->statusTransaksi->nama_status;
                        $customer->notify(new PushNotification($title, $body));
                    }
                }
            }

            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksis
            ], 200);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    public function showWithProducts($id)
    {
        try {
            $transaksi = Transaksi::with(['cart.detailCart.produk', 'cart.customer', 'cart.detailCart.hampers', 'statusTransaksi', 'alamat', 'packagings.bahanBaku'])->find($id);
            return response([
                'message' => 'Retrieve Success',
                'data' => $transaksi
            ], 200);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    public function indexMenungguKonfirmasi(Request $request)
    {
        try {
            $transaksiQuery = Transaksi::query()
                ->where('status_transaksi_id', 1)
                ->where('jenis_pengiriman', 'delivery')
                ->with(['cart.customer', 'statusTransaksi', 'packagings.bahanBaku', 'alamat']);
            if ($request->search) {
                $transaksiQuery->whereHas('cart.customer', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->search . '%');
                })->orwhereHas('statusTransaksi', function ($query) use ($request) {
                    $query->where('nama_status', 'like', '%' . $request->search . '%');
                })->orWhere('no_nota', 'like', '%' . $request->search . '%');
            }
            if ($request->date) {
                $transaksiQuery->whereDate('tanggal_pesan', $request->date);
            }

            if ($request->status) {
                $transaksiQuery->where('status_transaksi_id', $request->status);
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'total_harga', 'status', 'tanggal_pesan'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $transaksis = $transaksiQuery->orderBy($sortBy, $sortOrder)->get();

            //update status transaksi menjadi batal jika belum
            //kembalikan stok produk yang ready stock jika belum
            foreach ($transaksis as $transaksi) {
                //cek apakah setiap produk dalam transaksi ready stock
                $status_transaksi = "Ready Stock";
                foreach ($transaksi->cart->detailCart as $detailCart) {
                    if ($detailCart->status_produk == "Pre Order") {
                        $status_transaksi = "Pre Order";
                        break;
                    }
                }
                $tanggal_ambil = new DateTime($transaksi->tanggal_ambil);
                $tanggal_sekarang = new DateTime(Carbon::now()->addDay()->toDateString());
                if ($transaksi->status_transaksi_id != 12 && $transaksi->tanggal_lunas == null && $tanggal_ambil <= $tanggal_sekarang) {
                    //jika semua produk ready stock, tanggal ambil boleh = tanggal hari ini
                    //jadi transaksi yang semua produknya ready stock dan taggal ambil = tanggal hari ini bakal di remove
                    if ($status_transaksi == "Ready Stock") {
                        $tanggal_sekarang = new DateTime(Carbon::now()->toDateString());
                        if ($tanggal_ambil < $tanggal_sekarang) {
                            foreach ($transaksi->cart->detailCart as $detailCart) {
                                if ($detailCart->produk_id != null) {
                                    $produk = $detailCart->produk;
                                    $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                                    $produk->status = "Ready Stock";
                                    $produk->save();
                                } else {
                                    $hampers = $detailCart->hampers;
                                    foreach ($hampers->detailHampers as $detailHampers) {
                                        for ($i = 0; $i < $detailCart->jumlah; $i++) {
                                            $produk = $detailHampers->produk;
                                            $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                            $produk->status = "Ready Stock";
                                            $produk->save();
                                        }
                                    }
                                }
                            }
                            $customer = $transaksi->cart->customer;
                            $customer->poin = $customer->poin + $transaksi->poin_dipakai;
                            $customer->save();
                            $transaksi->status_transaksi_id = 12;
                            $transaksi->save();
                            $title = 'Status Transaksi Diperbaharui';
                            $body = $transaksi->statusTransaksi->nama_status;
                            $customer->notify(new PushNotification($title, $body));
                        }
                    } else if ($status_transaksi == "Pre Order") {
                        foreach ($transaksi->cart->detailCart as $detailCart) {
                            if ($detailCart->status_produk == "Ready Stock") {
                                if ($detailCart->produk_id != null) {
                                    $produk = $detailCart->produk;
                                    $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                                    $produk->status = "Ready Stock";
                                    $produk->save();
                                } else {
                                    $hampers = $detailCart->hampers;
                                    foreach ($hampers->detailHampers as $detailHampers) {
                                        for ($i = 0; $i < $detailCart->jumlah; $i++) {
                                            $produk = $detailHampers->produk;
                                            $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                            $produk->status = "Ready Stock";
                                            $produk->save();
                                        }
                                    }
                                }
                            }
                        }
                        $customer = $transaksi->cart->customer;
                        $customer->poin = $customer->poin + $transaksi->poin_dipakai;
                        $customer->save();
                        $transaksi->status_transaksi_id = 12;
                        $transaksi->save();
                        $title = 'Status Transaksi Diperbaharui';
                        $body = $transaksi->statusTransaksi->nama_status;
                        $customer->notify(new PushNotification($title, $body));
                    }
                }
            }

            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksis
            ], 200);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    // nanti sewaktu transaksi === diproses
    // jangan lupa tambah packaging 1x Tas Spunbond
    // kurangi stok bahan baku Tas Spunbond

    public function updateOngkir(Request $request, $id)
    {
        try {
            $transaksi = Transaksi::find($id);
            if (!$transaksi) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan.',
                    ],
                    404
                );
            }

            if ($transaksi->status_transaksi_id != 1) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => "Pesanan sudah dikonfirmasi, tidak bisa mengupdate ongkir.",
                    ],
                    400
                );
            }

            if ($transaksi->jenis_pengiriman == 'delivery' && $request->jarak <= 0) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Jarak pengiriman harus lebih dari 0',
                    ],
                    400
                );
            }

            $validate = Validator::make($request->all(), [
                'jarak' => 'required|gt:-1',
            ], [
                'jarak.gt' => 'Jarak pengiriman harus lebih dari 0',
                'jarak.required' => 'Jarak pengiriman harus diisi',
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

            if ($transaksi->jenis_pengiriman == 'pickup' && $request->jarak != 0) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Jarak pengiriman harus 0 untuk pengiriman pickup',
                    ],
                    400
                );
            }

            if ($request->jarak == 0) {
                $transaksi->ongkos_kirim = 0;
            } else if ($request->jarak <= 5) {
                $transaksi->ongkos_kirim = 10000;
            } else if ($request->jarak <= 10) {
                $transaksi->ongkos_kirim = 15000;
            } else if ($request->jarak <= 15) {
                $transaksi->ongkos_kirim = 20000;
            } else {
                $transaksi->ongkos_kirim = 25000;
            }

            $transaksi->total_harga = $transaksi->total_harga + $transaksi->ongkos_kirim;
            $transaksi->status_transaksi_id = 2;
            $transaksi->save();
            $customer = $transaksi->cart->customer;
            $title = 'Status Transaksi Diperbaharui';
            $body = $transaksi->statusTransaksi->nama_status;
            $customer->notify(new PushNotification($title, $body));

            return response()->json(
                [
                    'data' => $transaksi,
                    'message' => 'Berhasil mengupdate ongkir dan total harga Transaksi.',
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

    public function updatePembayaran(Request $request, $id)
    {
        try {
            $transaksi = Transaksi::find($id);
            if (!$transaksi) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan.',
                    ],
                    404
                );
            }

            if ($transaksi->status_transaksi_id != 3) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => "Status transaksi tidak sesuai, tidak bisa mengupdate pembayaran.",
                    ],
                    400
                );
            }

            $validate = Validator::make($request->all(), [
                'jumlah_pembayaran' => 'required|gt:0',
            ], [
                'jumlah_pembayaran.gt' => 'Jumlah pembayaran harus lebih dari 0',
                'jumlah_pembayaran.required' => 'Jumlah pembayaran harus diisi',
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

            if ($request->jumlah_pembayaran < $transaksi->total_harga) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Jumlah pembayaran kurang dari total harga (Rp' . $transaksi->total_harga . '), pembayaran tidak valid.',
                    ],
                    400
                );
            }

            $transaksi->tip = $request->jumlah_pembayaran - $transaksi->total_harga;
            $transaksi->tanggal_lunas = Carbon::now();
            $transaksi->status_transaksi_id = 4;
            $transaksi->save();
            $customer = $transaksi->cart->customer;
            $title = 'Status Transaksi Diperbaharui';
            $body = $transaksi->statusTransaksi->nama_status;
            $customer->notify(new PushNotification($title, $body));
            return response()->json(
                [
                    'data' => $transaksi,
                    'message' => 'Berhasil mengupdate pembayaran dan tip Transaksi.',
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

    public function updateStatusTransaksi($id)
    {
        try {
            $transaksi = Transaksi::find($id);
            if (!$transaksi) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan'
                    ],
                    404
                );
            }

            if ($transaksi->status_transaksi_id != 7 && $transaksi->status_transaksi_id != 8) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak sedang diproses/dipickup, tidak bisa mengupdate status'
                    ],
                    400
                );
            }
            if ($transaksi->status_transaksi_id == 8) {
                $transaksi->status_transaksi_id = 10;
            } else if ($transaksi->status_transaksi_id == 7) {
                if ($transaksi->jenis_pengiriman == 'pickup') {
                    $transaksi->status_transaksi_id = 8;
                } else {
                    $transaksi->status_transaksi_id = 9;
                }
            }
            $customer = $transaksi->cart->customer;
            $title = 'Status Transaksi Diperbaharui';
            $body = $transaksi->statusTransaksi->nama_status;
            $customer->notify(new PushNotification($title, $body));
            $transaksi->save();
            return response()->json(
                [
                    'data' => $transaksi,
                    'message' => 'Berhasil mengupdate status transaksi menjadi ' . $transaksi->statusTransaksi->nama_status,
                ]
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

    public function indexDiproses(Request $request)
    {
        try {
            $transaksiQuery = Transaksi::query()
                ->where('status_transaksi_id', 6)
                ->orWhere('status_transaksi_id', 7)
                ->orWhere('status_transaksi_id', 8)
                ->orWhere('status_transaksi_id', 9)
                ->orWhere('status_transaksi_id', 10)
                ->with(['cart.customer', 'statusTransaksi', 'packagings.bahanBaku', 'alamat']);
            if ($request->search) {
                $transaksiQuery->whereHas('cart.customer', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->search . '%');
                })->orwhereHas('statusTransaksi', function ($query) use ($request) {
                    $query->where('nama_status', 'like', '%' . $request->search . '%');
                })->orWhere('no_nota', 'like', '%' . $request->search . '%');
            }
            if ($request->date) {
                $transaksiQuery->whereDate('tanggal_pesan', $request->date);
            }

            if ($request->status) {
                $transaksiQuery->where('status_transaksi_id', $request->status);
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'total_harga', 'status', 'tanggal_pesan'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $transaksis = $transaksiQuery->orderBy($sortBy, $sortOrder)->get();

            return response(
                [
                    'message' => 'Retrieve All Success',
                    'data' => $transaksis
                ],
                200
            );
        } catch (Throwable $e) {
            return response(
                [
                    'message' => $e->getMessage(),
                    'data' => null
                ],
                404
            );
        }
    }

    public function indexTelatBayar(Request $request)
    {
        try {
            $transaksiQuery = Transaksi::query()
                ->where('tanggal_ambil', '<=', Carbon::now()->addDay())
                ->whereNull('tanggal_lunas')
                ->with(['cart.customer', 'statusTransaksi', 'packagings.bahanBaku', 'alamat', 'cart.detailCart.produk', 'cart.detailCart.hampers.detailHampers.produk']);

            if ($request->search) {
                $transaksiQuery->whereHas('cart.customer', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->search . '%');
                })->orwhereHas('statusTransaksi', function ($query) use ($request) {
                    $query->where('nama_status', 'like', '%' . $request->search . '%');
                })->orWhere('no_nota', 'like', '%' . $request->search . '%');
            }

            if ($request->date) {
                $transaksiQuery->whereDate('tanggal_pesan', $request->date);
            }

            if ($request->status) {
                $transaksiQuery->where('status_transaksi_id', $request->status);
            }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'total_harga', 'status', 'tanggal_pesan'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            $transaksis = $transaksiQuery->orderBy($sortBy, $sortOrder)->get();

            //update status transaksi menjadi batal jika belum
            //kembalikan stok produk yang ready stock jika belum
            foreach ($transaksis as $transaksi) {
                //cek apakah setiap produk dalam transaksi ready stock
                $status_transaksi = "Ready Stock";
                foreach ($transaksi->cart->detailCart as $detailCart) {
                    if ($detailCart->status_produk == "Pre Order") {
                        $status_transaksi = "Pre Order";
                        break;
                    }
                }
                if ($transaksi->status_transaksi_id != 12) {
                    //jika semua produk ready stock, tanggal ambil boleh = tanggal hari ini
                    //jadi transaksi yang semua produknya ready stock dan taggal ambil = tanggal hari ini bakal di remove
                    if ($status_transaksi == "Ready Stock") {
                        $tanggal_ambil = new DateTime($transaksi->tanggal_ambil);
                        $tanggal_sekarang = new DateTime(Carbon::now()->toDateString());
                        if ($tanggal_ambil < $tanggal_sekarang) {
                            foreach ($transaksi->cart->detailCart as $detailCart) {
                                if ($detailCart->produk_id != null) {
                                    $produk = $detailCart->produk;
                                    $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                                    $produk->status = "Ready Stock";
                                    $produk->save();
                                } else {
                                    $hampers = $detailCart->hampers;
                                    foreach ($hampers->detailHampers as $detailHampers) {
                                        for ($i = 0; $i < $detailCart->jumlah; $i++) {
                                            $produk = $detailHampers->produk;
                                            $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                            $produk->status = "Ready Stock";
                                            $produk->save();
                                        }
                                    }
                                }
                            }
                            $customer = $transaksi->cart->customer;
                            $customer->poin = $customer->poin + $transaksi->poin_dipakai;
                            $customer->save();
                            $transaksi->status_transaksi_id = 12;
                            $transaksi->save();
                            $title = 'Status Transaksi Diperbaharui';
                            $body = $transaksi->statusTransaksi->nama_status;
                            $customer->notify(new PushNotification($title, $body));
                        } else {
                            //keluarkan transaksi yang tanggal ambilnya belum lewat
                            $transaksis = $transaksis->reject(function ($item) use ($transaksi) {
                                return $item->id === $transaksi->id;
                            });
                        }
                    } else if ($status_transaksi == "Pre Order") {
                        foreach ($transaksi->cart->detailCart as $detailCart) {
                            if ($detailCart->status_produk == "Ready Stock") {
                                if ($detailCart->produk_id != null) {
                                    $produk = $detailCart->produk;
                                    $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                                    $produk->status = "Ready Stock";
                                    $produk->save();
                                } else {
                                    $hampers = $detailCart->hampers;
                                    foreach ($hampers->detailHampers as $detailHampers) {
                                        for ($i = 0; $i < $detailCart->jumlah; $i++) {
                                            $produk = $detailHampers->produk;
                                            $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                            $produk->status = "Ready Stock";
                                            $produk->save();
                                        }
                                    }
                                }
                            }
                        }
                        $customer = $transaksi->cart->customer;
                        $customer->poin = $customer->poin + $transaksi->poin_dipakai;
                        $customer->save();
                        $transaksi->status_transaksi_id = 12;
                        $transaksi->save();
                        $title = 'Status Transaksi Diperbaharui';
                        $body = $transaksi->statusTransaksi->nama_status;
                        $customer->notify(new PushNotification($title, $body));
                    }
                }
            }

            return response(
                [
                    'message' => 'Retrieve All Success',
                    'data' => $transaksis
                ],
                200
            );
        } catch (Throwable $e) {
            return response(
                [
                    'message' => $e->getMessage(),
                    'data' => null
                ],
                404
            );
        }
    }

    public function updateTerimaTolak($id, Request $request)
    {
        try {
            $transaksi = Transaksi::find($id);
            if (!$transaksi) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => "tidak ada transaksi dengan id tersebut"
                    ],
                    500
                );
            }
            if ($transaksi->status_transaksi_id != 4) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => "Pembayaran Transaksi Belum dilakukan/divalidasi"
                    ]
                );
            }

            if (!$request->verdict) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => "Verdict harus diisi"
                    ],
                    400
                );
            }


            if ($request->verdict == 'terima') {

                //tambah poin customer
                $customer = $transaksi->cart->customer;
                $customer->poin += $transaksi->poin_didapat;
                $customer->save();

                //ganti transaksi status jadi diterima
                $transaksi->status_transaksi_id = 6;
                $transaksi->save();
                $title = 'Status Transaksi Diperbaharui';
                $body = $transaksi->statusTransaksi->nama_status;
                $customer->notify(new PushNotification($title, $body));

                //update stock produk


                //ambil bahan baku dari tiap transaksi
                // $bahanbutuh = array();
                // $produkIds = $transaksi->cart->detailCart->produk->pluck('id');
                // foreach ($produkIds as $produkId) {
                //     $produk = Produk::find($produkId);
                //     $resep = $produk->resep;
                //     $detailReseps = $resep->detailResep;
                //     foreach ($detailReseps as $detailResep) {
                //         $bahanbutuh[] = [
                //             'nama_bahan_baku' => $detailResep->bahanBaku->nama_bahan_baku,
                //             'jumlah_bahan_resep' => $detailResep->jumlah_bahan_resep,
                //         ];
                //     }
                // }


                return response()->json(
                    [
                        'data' => $transaksi,
                        'message' => "Transaksi berhasil diterima"
                    ]
                );
            } else if ($request->verdict == 'tolak') {
                //ganti status jadi ditolak
                $transaksi->status_transaksi_id = 5;

                //update saldo customer
                $transaksi->cart->customer->saldo += $transaksi->total_harga;
                $transaksi->cart->customer->saldo += $transaksi->tip;
                $transaksi->cart->customer->poin += $transaksi->poin_dipakai;

                //update stok produk
                foreach ($transaksi->cart->detailCart as $detailCart) {
                    if ($detailCart->status_produk == "Ready Stock") {
                        if ($detailCart->produk_id != null) {
                            $produk = $detailCart->produk;
                            $produk->jumlah_stock = $produk->jumlah_stock + $detailCart->jumlah;
                            $produk->status = "Ready Stock";
                            $produk->save();
                        } else {
                            $hampers = $detailCart->hampers;
                            foreach ($hampers->detailHampers as $detailHampers) {
                                $produk = $detailHampers->produk;
                                $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                $produk->status = "Ready Stock";
                                $produk->save();
                            }
                        }
                    }
                }

                $transaksi->cart->customer->save();
                $transaksi->save();
                $customer = $transaksi->cart->customer;
                $title = 'Status Transaksi Diperbaharui';
                $body = $transaksi->statusTransaksi->nama_status;
                $customer->notify(new PushNotification($title, $body));
                return response()->json(
                    [
                        'data' => $transaksi,
                        'message' => "Transaksi berhasil ditolak"
                    ]
                );
            }
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

    // public function sortBahanBakuList($bahanbutuhkan){
    //     //sort dulu bahannya baru masuk ke transaksi
    //     function sortByName($a, $b) {
    //         return strcmp($a['nama_bahan_baku'], $b['nama_bahan_baku']);
    //     }
    //     usort($bahanbutuhkan, "sortByName");
    //     return $bahanbutuhkan;
    // }


    public function warnBahanBaku($id){
        try{
            $transaksi = Transaksi::query()->where('id', $id)->with(['cart.customer', 'cart.detailCart.produk.kategoriProduk', 'cart.detailCart.hampers.detailHampers.produk.kategoriProduk'])->get();
            if(!$transaksi){
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan'
                    ]
                    );
            }
            $trfArr[] = [
                'transaksi_arr' => $transaksi,
            ];

            $res = app('App\Http\Controllers\Api\PemrosesanPesananController')->checkPesananHarian($transaksi);

            $data = $res['rekap_bahan'];

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Berhasil mendapatkan data bahan baku pada transaksi'
                ]
                );

        }catch(Throwable $e){
            return response()->json(
                [
                    'data' => null,
                    'message' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function BahanBakuTransaksi()
    {
        try {
            // dummy biar banyak datanya
            // $transaksis = Transaksi::all();
            //ril punya
            $transaksis = Transaksi::all()->where('status_transaksi_id', 6)->where('tanggal_ambil', '>=', Carbon::now());
            $transaksiperTanggal = [];
            foreach ($transaksis as $transaksi) {
                $found = false;
                // echo "adding new id transaksi: " . $transaksi->id . "\n";
                //cek di array, udaah ada ato blom tanggal ambil yang sama
                foreach ($transaksiperTanggal as &$group) {
                    if ($group["tanggal_ambil"] === $transaksi->tanggal_ambil) {
                        $group["id_transaksi"][] = $transaksi->id;
                        $group["no_nota"][] = $transaksi->no_nota;
                        $found = true;
                        break;
                    }
                }


                if (!$found) {
                    // echo "Adding new tanggal ambil: " . $transaksi->tanggal_ambil . "\n";
                    $transaksiperTanggal[] = [
                        'tanggal_ambil' => $transaksi->tanggal_ambil,
                        'id_transaksi' => [$transaksi->id],
                        'no_nota' => [$transaksi->no_nota]
                    ];
                }
            }

            //cari cart nya semua
            foreach ($transaksiperTanggal as &$transaksiTanggal) {
                $bahanbutuh = [];
                foreach ($transaksiTanggal["id_transaksi"] as $idTransaksi) {
                    $transaksi = Transaksi::find($idTransaksi);
                    $produkIds = [];
                    $detailCarts = $transaksi->cart->detailCart;

                    foreach ($detailCarts as $detailCart) {
                        if ($detailCart->produk && $detailCart->produk->resep) {
                            // echo "Produk: " . $detailCart->produk->nama_produk . "\n";
                            $produkIds[] = [
                                'id' => $detailCart->produk->id,
                                'jumlah' => $detailCart->jumlah
                            ];
                        } else if ($detailCart->hampers) {
                            // echo "Hampers: " . $detailCart->hampers->nama_hampers . "\n";
                            $hamperId = $detailCart->hampers->id;
                            $hamper = Hampers::find($hamperId);
                            $detailhampers = $hamper->detailHampers;
                            foreach ($detailhampers as $detailHampers) {
                                // echo "Produk: " . $detailHampers->produk->nama_produk . "\n";
                                if ($detailHampers->produk && $detailHampers->produk->resep) {
                                    $produkIds[] = [
                                        'id' => $detailHampers->produk->id,
                                        'jumlah' => $detailHampers->jumlah_produk * $detailCart->jumlah
                                    ];
                                }
                            }
                        }
                    }
                    foreach ($produkIds as $produkId) {
                        // echo "Produk ID: " . $produkId['id'] . "\n";
                        // echo "Jumlah: " . $produkId['jumlah'] . "\n";
                        $produk = Produk::find($produkId['id']);
                        $resep = $produk->resep;
                        $detailReseps = $resep->detailResep;
                        foreach ($detailReseps as $detailResepa) {
                            $namaBahanBaku = $detailResepa->bahanBaku->nama_bahan_baku;
                            $jumlahBahanResep = $detailResepa->jumlah_bahan_resep;
                            // echo "Checking ingredient: $namaBahanBaku with amount: $jumlahBahanResep\n";
                            $found = false;
                            // cek dulu ada ato ndak di array
                            foreach ($bahanbutuh as &$bahan) {
                                // echo "Comparing with existing ingredient: " . $bahan['nama_bahan_baku'] . "\n";
                                if ($bahan['nama_bahan_baku'] === $namaBahanBaku) {
                                    $bahan['jumlah_bahan_resep'] += ($jumlahBahanResep * $produkId['jumlah']);
                                    $found = true;
                                    // echo "\n" . $bahan['nama_bahan_baku'] . " found\n";
                                    break;
                                }
                                // echo "ga ketemu";
                            }
                            // klo gk ketemu, tambahin ke array
                            if ($found == false) {
                                $bahanbutuh[] = [
                                    'id_bahan_baku' => $detailResepa->bahanBaku->id,
                                    'nama_bahan_baku' => $detailResepa->bahanBaku->nama_bahan_baku,
                                    'satuan' => $detailResepa->bahanBaku->satuan_bahan,
                                    'jumlah_bahan_resep' => ($jumlahBahanResep * $produkId['jumlah'])
                                ];
                                // echo "Added new ingredient: $namaBahanBaku\n";
                            }
                        }
                    }
                }
                foreach ($bahanbutuh as &$bahan) {
                    $bahanBaku = BahanBaku::find($bahan['id_bahan_baku']);
                    // echo "Checking stock of " . $bahanBaku->nama_bahan_baku . " with amount: " . $bahan['jumlah_bahan_resep'] . "\n";
                    if ($bahanBaku->jumlah_bahan_baku < $bahan['jumlah_bahan_resep']) {
                        // echo "Bahan baku tidak cukup\n";
                        $bahan['warn'] = [
                            'message' => 'Bahan baku tidak cukup',
                            'jumlah_bahan_stock' => $bahanBaku->jumlah_bahan_baku
                        ];
                    }
                }
                // $this->sortBahanBakuList($bahanbutuh);
                $transaksiTanggal['bahan_baku'] = $bahanbutuh;
            }


            return response()->json(
                [
                    'data' => $transaksiperTanggal,
                    'message' => "Berhasil mengambil bahan baku dari transaksi"
                ]
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

    public function updateBukti($id, Request $request)
    {
        try {
            $transaksi = Transaksi::find($id);
            if (!$transaksi) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => "tidak ada transaksi dengan id tersebut"
                    ],
                    500
                );
            }

            $validate = Validator::make($request->all(), [
                'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                'bukti_pembayaran.required' => 'Bukti Pembayaran harus diisi',
                'bukti_pembayaran.image' => 'Bukti Pembayaran harus berupa gambar',
                'bukti_pembayaran.mimes' => 'Bukti Pembayaran harus berupa gambar dengan format jpeg, png, jpg, gif, atau svg',
                'bukti_pembayaran.max' => 'Bukti Pembayaran tidak boleh lebih dari 2MB',
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

            //hapus foto lama kalo misalnya udah ada
            if ($transaksi->status_transaksi_id == 3) {
                if (!is_null($transaksi->kode_bukti_bayar) && Storage::disk('public')->exists($transaksi->kode_bukti_bayar)) {
                    Storage::disk('public')->delete($transaksi->kode_bukti_bayar);
                }
            }

            $uploadFolder = "/bukti_pembayaran";

            $isiRequest = $request->all();
            if ($request->file('bukti_pembayaran')) {
                $gambar = $isiRequest['bukti_pembayaran'];
                $path = $gambar->store($uploadFolder, 'public');

                $transaksi->kode_bukti_bayar = $path;
                $transaksi->status_transaksi_id = 3;
                $transaksi->tanggal_lunas = Carbon::now();
                $transaksi->save();
                $customer = $transaksi->cart->customer;
                $title = 'Status Transaksi Diperbaharui';
                $body = $transaksi->statusTransaksi->nama_status;
                $customer->notify(new PushNotification($title, $body));
            }

            return response()->json(
                [
                    'data' => $transaksi,
                    'message' => "Berhasil mengupload bukti pembayaran"
                ]
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
}
