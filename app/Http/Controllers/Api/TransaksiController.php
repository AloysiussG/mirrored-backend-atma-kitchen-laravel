<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hampers;
use App\Models\Produk;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;
use DateTime;
use Throwable;
use Illuminate\Support\Facades\Validator;

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

            if ($transaksi->jenis_pengiriman == 'pickup') {
                if ($request->jarak != 0) {
                    return response()->json(
                        [
                            'data' => null,
                            'message' => 'Jarak pengiriman harus 0 untuk pengiriman pickup',
                        ],
                        400
                    );
                }
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
                                        $produk = $detailHampers->produk;
                                        $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                        $produk->status = "Ready Stock";
                                        $produk->save();
                                    }
                                }
                            }
                            $transaksi->status_transaksi_id = 12;
                            $transaksi->save();
                        } else {
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
                                        $produk = $detailHampers->produk;
                                        $produk->jumlah_stock = $produk->jumlah_stock + $detailHampers->jumlah_produk;
                                        $produk->status = "Ready Stock";
                                        $produk->save();
                                    }
                                }
                            }
                        }
                        $transaksi->status_transaksi_id = 12;
                        $transaksi->save();
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
}
