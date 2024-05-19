<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CartController extends Controller
{


    public function cekKetersediaanByTanggalAmbil(Request $request)
    {
        try {
            $user = $request->user();

            // bukan customer kalau punya role, throw 401
            if (isset($user['role_id'])) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'User bukan customer, tidak dapat melihat cart.'
                    ],
                    401
                );
            }

            $cartQuery = Cart::query()
                ->with(['detailCart.produk', 'detailCart.hampers'])
                ->withCount('detailCart')
                ->withSum('detailCart as detail_cart_sum', 'jumlah')
                ->where('customer_id', $user->id)
                ->where('status_cart', 1); // cart yang sedang aktif

            $cart = $cartQuery->orderBy('id', 'desc')->first(); // karena 1 aja cartnya

            $cekRequest = $request->all();

            // TODO::: VALIDATOR LEBIH RUMIT & AMAN
            $validate = Validator::make($cekRequest, [
                'tanggal_ambil' => 'required',
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

            $detailCartMapped = $cart->detailCart->map(function ($detailItem) use ($cekRequest) {
                $transaksiCount = 0;

                // get produk/hampers dari detail cart item singular
                if ($detailItem->produk) {
                    $item = $detailItem->produk;
                    $transaksiObj = Transaksi::query()
                        ->whereHas('cart.detailCart.produk', function ($query) use ($item) {
                            $query->where('id',  $item->id);
                        })
                        ->with('cart', function ($query) use ($item) {
                            // $query->withCount('detailCart');
                            $query->withSum(['detailCart' => function ($q) use ($item) {
                                $q->where('produk_id', $item->id);
                            }], 'jumlah');
                        })
                        ->where('tanggal_ambil', $cekRequest['tanggal_ambil'])
                        ->whereNotIn('status_transaksi_id', [5, 12])
                        ->get();
                    $transaksiCount = $transaksiObj->sum('cart.detail_cart_sum_jumlah') ?? 0;
                } else if ($detailItem->hampers) {
                    $item = $detailItem->hampers;
                    $transaksiObj = Transaksi::query()
                        ->whereHas('cart.detailCart.hampers', function ($query) use ($item) {
                            $query->where('id',  $item->id);
                        })
                        ->with('cart', function ($query) use ($item) {
                            // $query->withCount('detailCart');
                            $query->withSum(['detailCart' => function ($q) use ($item) {
                                $q->where('produk_id', $item->id);
                            }], 'jumlah');
                        })
                        ->where('tanggal_ambil', $cekRequest['tanggal_ambil'])
                        ->whereNotIn('status_transaksi_id', [5, 12])
                        ->get();
                    $transaksiCount = $transaksiObj->sum('cart.detail_cart_sum_jumlah') ?? 0;
                }

                $item_info['count_transaksi_that_day'] = $transaksiCount;

                $sisaKuota = $item['kuota_harian'] - $transaksiCount;
                if ($sisaKuota < 0) {
                    $sisaKuota = 0;
                }

                $item_info['sisa_kuota_harian'] = $sisaKuota;

                $detailItem['item_info'] = $item_info;

                return $detailItem;
            });

            $cart->detailCart = $detailCartMapped;

            return response()->json(
                [
                    'data' => $cart,
                    'message' => 'Berhasil mengecek data cart.'
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

    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // bukan customer kalau punya role, throw 401
            if (isset($user['role_id'])) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'User bukan customer, tidak dapat melihat cart.'
                    ],
                    401
                );
            }

            $cartQuery = Cart::query()
                ->with(['detailCart.produk.kategoriProduk', 'detailCart.hampers.detailHampers.produk.kategoriProduk'])
                ->withCount('detailCart')
                ->withSum('detailCart as detail_cart_sum', 'jumlah')
                ->where('customer_id', $user->id)
                ->where('status_cart', 1); // cart yang sedang aktif

            // if ($request->search) {
            //     $cartQuery->where('nama_hampers', 'like', '%' . $request->search . '%');
            // }

            // if ($request->produk) {
            //     $cartQuery->whereHas('detailHampers.produk', function ($query) use ($request) {
            //         $query->where('nama_produk', 'like', '%' . $request->produk . '%');
            //     });
            // }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_hampers', 'created_at', 'harga_hampers'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            // // SORT METHOD #2
            // $arraySort = [
            //     'terbaru',
            //     'terlama',
            //     'harga tertinggi',
            //     'harga terendah',
            //     'stok terbanyak',
            //     'kuota terbanyak',
            // ];

            // $arraySortValue = [
            //     ['name' => "Terbaru", 'sortBy' => "id", 'sortOrder' => "desc"],
            //     ['name' => "Terlama", 'sortBy' => "id", 'sortOrder' => "asc"],
            //     ['name' => "Harga tertinggi", 'sortBy' => "harga_hampers", 'sortOrder' => "desc"],
            //     ['name' => "Harga terendah", 'sortBy' => "harga_hampers", 'sortOrder' => "asc"],
            // ];

            // if ($request->sort && in_array(strtolower($request->sort), $arraySort)) {
            //     $key = array_search(strtolower($request->sort), $arraySort);
            //     $sortBy = $arraySortValue[$key]['sortBy'];
            //     $sortOrder = $arraySortValue[$key]['sortOrder'];
            // }

            $cart = $cartQuery->orderBy($sortBy, $sortOrder)->first(); // karena 1 aja cartnya

            // hitung subtotal sebelum checkout
            // dihitung dari base harga produk/hampers, bukan dari harga_produk_sekarang
            $total = 0;
            if ($cart->detailCart) {
                foreach ($cart->detailCart as $item) {
                    if ($item->hampers) {
                        $total = $total + ($item->jumlah * $item->hampers->harga_hampers);
                    } else if ($item->produk) {
                        $total = $total + ($item->jumlah * $item->produk->harga);
                    }
                }
            }

            $cart['subtotal'] = $total;

            // KALO ACTIVE CART TIDAK DITEMUKAN
            // TODO::: bikin cart baru ???

            if ($request->tanggal_ambil) {
                // get seluruh rekap produk di cart
                $allProduksCart = [];
                $newArr = [];

                foreach ($cart->detailCart as $detailItem) {
                    if (isset($detailItem->produk)) {
                        $newArr = $detailItem['produk'];
                        $newArr['jumlah'] = $detailItem['jumlah'];
                        $allProduksCart[] = $newArr;
                    } else if (isset($detailItem->hampers)) {
                        foreach ($detailItem->hampers->detailHampers as $detailHampers) {
                            $newArr = $detailHampers['produk'];
                            $newArr['jumlah'] = $detailItem['jumlah'] * $detailHampers['jumlah_produk'];
                            $allProduksCart[] = $newArr;
                        }
                    }
                }

                $allProduksCartCollection = collect($allProduksCart);
                $groupedAllProduks = $allProduksCartCollection->groupBy('id');
                $allProduksCartResults = $groupedAllProduks->map(function ($group) {
                    $firstItem = $group->first();
                    $firstItem['jumlah'] = $group->sum('jumlah');
                    return $firstItem;
                })->values();

                // ------------------

                // get produk di banyak transaksi pada tanggal ambil
                $allProduksTransaksi = [];
                $newArr = [];

                $transaksiArr = Transaksi::query()
                    ->with(['cart.detailCart.produk.kategoriProduk', 'cart.detailCart.hampers.detailHampers.produk.kategoriProduk'])
                    ->where('tanggal_ambil', $request->tanggal_ambil)
                    ->whereNotIn('status_transaksi_id', [5, 12])
                    ->get();

                foreach ($transaksiArr as $value) {
                    $detailCarts = $value->cart->detailCart;
                    foreach ($detailCarts as $detailItem) {
                        if (isset($detailItem->produk)) {
                            $newArr['id'] = $detailItem['produk']['id'];
                            $newArr['jumlah_dibeli'] = $detailItem['jumlah'];
                            $allProduksTransaksi[] = $newArr;
                        } else if (isset($detailItem->hampers)) {
                            foreach ($detailItem->hampers->detailHampers as $detailHampers) {
                                $newArr['id'] = $detailHampers['produk']['id'];
                                $newArr['jumlah_dibeli'] = $detailItem['jumlah'] * $detailHampers['jumlah_produk'];
                                $allProduksTransaksi[] = $newArr;
                            }
                        }
                    }
                }

                $collection = collect($allProduksTransaksi);
                $grouped = $collection->groupBy('id');

                $allProduksTransaksiResults = $grouped->map(function ($group, $id) {
                    return [
                        'id' => $id,
                        'jumlah_dibeli' => $group->sum('jumlah_dibeli')
                    ];
                })->values();

                $allWithSisaKuota = $allProduksCartResults->map(function ($itemProduk) use ($allProduksTransaksiResults) {
                    $jumlahDibeli = 0;
                    $found = $allProduksTransaksiResults->firstWhere('id', $itemProduk->id);
                    if ($found) {
                        $jumlahDibeli = $found['jumlah_dibeli'];
                    }
                    $itemProduk['jumlah_pembelian_pada_tanggal_ambil'] = $jumlahDibeli;
                    $itemProduk['sisa_kuota_harian'] = $itemProduk->kuota_harian - $jumlahDibeli;
                    return $itemProduk;
                });

                $cart['another'] = $allWithSisaKuota;
            }

            return response()->json(
                [
                    'data' => $cart,
                    'message' => 'Berhasil mengambil data cart.'
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
