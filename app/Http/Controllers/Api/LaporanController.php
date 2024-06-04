<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\StatusTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexLaporanPenjualanBulananPerProduk(Request $request)
    {
        try {
            $user = $request->user();

            $validate = Validator::make($request->all(), [
                'num_month' => 'required|numeric|min:1|max:12',
                'num_year' => 'required|numeric|min_digits:4|max_digits:4',
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

            $numMonth = (int)$request->num_month;
            $numYear = (int)$request->num_year;

            $statusRes = StatusTransaksi::query()
                ->where('nama_status', 'like', '%selesai%')
                ->first();

            $transaksiArr = Transaksi::query()
                ->with(['cart.customer', 'cart.detailCart.produk.kategoriProduk', 'cart.detailCart.hampers.detailHampers.produk.kategoriProduk'])
                ->whereMonth('tanggal_pesan', $numMonth)
                ->whereYear('tanggal_pesan', $numYear)
                ->where('status_transaksi_id', $statusRes->id)
                ->orderBy('id', 'asc')
                ->get();

            $detailCart = $transaksiArr->flatMap(function ($item) {
                return $item->cart->detailCart;
            });

            $detailCartProdukHampers = $detailCart->map(function ($detailItem) {
                $newArr = collect([]);
                if (isset($detailItem->produk)) {
                    $newArr['nama_produk'] = $detailItem->produk->nama_produk;
                } else if (isset($detailItem->hampers)) {
                    $newArr['nama_produk'] = $detailItem->hampers->nama_hampers;
                }
                $newArr['jumlah_dibeli'] = $detailItem->jumlah;
                $newArr['harga_produk_sekarang'] = $detailItem->harga_produk_sekarang;
                return $newArr;
            });

            $detailCartProdukHampersGrouped = $detailCartProdukHampers->groupBy(['nama_produk', 'harga_produk_sekarang']);
            $detailCartProdukHampersResults = $detailCartProdukHampersGrouped->flatMap(function ($group) {
                $item = $group->map(function ($group2) {
                    $item2 = $group2->first();
                    $item2['jumlah_dibeli'] = $group2->sum('jumlah_dibeli');
                    $item2['jumlah_x_harga'] = $item2['jumlah_dibeli'] * $item2['harga_produk_sekarang'];
                    return $item2;
                })->values();
                return $item;
            })->sortBy('nama_produk')->values();

            $data['bulan'] = Carbon::create()->month($numMonth)->format('F');
            $data['tahun'] = Carbon::create()->year($numYear)->format('Y');
            $data['tanggal_cetak'] = Carbon::now();
            $data['dicetak_oleh'] = $user->nama . ' (' . $user->role->role_name . ')';
            $data['transaksi_count'] = count($transaksiArr);
            $data['produk_arr_count'] = count($detailCartProdukHampersResults);
            $data['produk_arr'] = $detailCartProdukHampersResults;
            $data['sum'] = $detailCartProdukHampersResults->sum('jumlah_x_harga');

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Berhasil mengambil laporan penjualan bulanan per produk.'
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
     * Display a listing of the resource.
     */
    public function indexLaporanStokBahanBaku(Request $request)
    {
        try {
            $user = $request->user();

            $bahanBakuArr = BahanBaku::query()
                ->orderBy('jumlah_bahan_baku', 'desc')
                ->get();

            $data['tanggal_cetak'] = Carbon::now();
            $data['dicetak_oleh'] = $user->nama . ' (' . $user->role->role_name . ')';
            $data['bahan_baku_arr_count'] = count($bahanBakuArr);
            $data['bahan_baku_arr'] = $bahanBakuArr;

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Berhasil mengambil laporan stok bahan baku.'
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
