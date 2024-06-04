<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        // -- 2.	Laporan penjualan bulanan per produk

        // -- kalau ada hampersnya
        // SELECT 
        //     IFNULL(p.nama_produk, h.nama_hampers) AS ProdukORHampers,
        //     SUM(dc.jumlah) AS Kuantitas, 
        //     dc.harga_produk_sekarang, 
        //     SUM(dc.harga_produk_sekarang * dc.jumlah) AS "Jumlah Uang",
        //     st.nama_status
        // FROM transaksis t
        // JOIN status_transaksis st ON (st.id = t.status_transaksi_id)
        // JOIN carts c ON (t.cart_id = c.id)
        // JOIN detail_carts dc ON (c.id = dc.cart_id)
        // LEFT JOIN produks p ON (p.id = dc.produk_id)
        // LEFT JOIN hampers h ON (h.id = dc.hampers_id)
        // WHERE 
        //     MONTHNAME(t.tanggal_pesan) = 'February' 
        //     AND YEAR(t.tanggal_pesan) = '2024'
        //     AND st.nama_status = 'Pesanan selesai'
        // GROUP BY ProdukORHampers 
        // WITH ROLLUP;


        // -- kalau semua jadi satu per produk (TAPI GAK MAKE SENSE DI JUMLAH UANG)

        // SELECT 
        //     IFNULL(p.nama_produk, pdh.nama_produk) AS Produk,
        //     SUM(IFNULL(dh.jumlah_produk, dc.jumlah)) AS Kuantitas,
        //     dc.harga_produk_sekarang, 
        //     SUM(dc.harga_produk_sekarang * dc.jumlah) AS "Jumlah Uang"
        // FROM transaksis t
        // JOIN status_transaksis st ON (st.id = t.status_transaksi_id)
        // JOIN carts c ON (t.cart_id = c.id)
        // JOIN detail_carts dc ON (c.id = dc.cart_id)
        // LEFT JOIN produks p ON (p.id = dc.produk_id)
        // LEFT JOIN hampers h ON (h.id = dc.hampers_id)
        // LEFT JOIN detail_hampers dh ON (h.id = dh.hampers_id)
        // LEFT JOIN produks pdh ON (pdh.id = dh.produk_id)
        // WHERE 
        //     MONTHNAME(t.tanggal_pesan) = 'February' 
        //     AND YEAR(t.tanggal_pesan) = '2024'
        //     AND st.nama_status = 'Pesanan selesai'
        // GROUP BY Produk 
        // WITH ROLLUP;



    }

    /**
     * Display a listing of the resource.
     */
    public function indexLaporanStokBahanBaku()
    {
        //
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
