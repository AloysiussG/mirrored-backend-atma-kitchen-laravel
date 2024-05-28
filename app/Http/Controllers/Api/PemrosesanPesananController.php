<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatusTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class PemrosesanPesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getArrPesananHarian()
    {
        // list pesanan harian yang perlu diproses hari ini
        // hari ini diproses === h-1 tanggal ambil
        // alias hari ini + 1 = tanggal ambil
        $today = Carbon::now()->format('Y-m-d');
        $todayPlusOne = Carbon::now()->addDay()->format('Y-m-d');

        // // {SEMENTARA ONLY BUAT TES}
        // $today = Carbon::parse('2024-05-26')->format('Y-m-d');
        // $todayPlusOne = Carbon::parse('2024-05-26')->addDay()->format('Y-m-d');

        // ambil status 'Pesanan diterima' (just in case)
        $statusRes = StatusTransaksi::query()
            ->where('nama_status', 'like', '%diterima%')
            ->first();

        // ambil transaksi yang sesuai tanggal & status transaksi = Pesanan diterima
        $transaksiArr = Transaksi::query()
            ->with(['cart.customer', 'cart.detailCart.produk.kategoriProduk', 'cart.detailCart.hampers.detailHampers.produk.kategoriProduk'])
            ->whereDate('tanggal_ambil', $todayPlusOne)
            ->where('status_transaksi_id', $statusRes->id)
            ->orderBy('id', 'asc')
            ->get();

        return [
            'today' => $today,
            'today_plus_one' => $todayPlusOne,
            'transaksi_arr' => $transaksiArr,
        ];
    }

    // list transaksi harian ---> untuk confirm proses/tidak
    public function indexTransaksiPerluDiproses()
    {
        try {
            $res = $this->getArrPesananHarian();
            $transaksiArr =  $res['transaksi_arr'];

            return response()->json(
                [
                    'data' => $transaksiArr,
                    'message' => 'Berhasil ambil data list transaksi yang perlu diproses.'
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

    public function prosesTransaksi(string $id)
    {
        try {
            $res = $this->getArrPesananHarian();
            $transaksiCollection =  collect($res['transaksi_arr']);
            $found = $transaksiCollection->firstWhere('id', $id);

            // cek apakah id transaksi dari URL ada di dalam list transaksi hari ini, just in case
            if (!$found) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
                    ],
                    400
                );
            }

            // TODO:: check warning bahan baku
            // ...
            $checked = true;

            if (!$checked) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak dapat diproses karena ada bahan baku yang kurang.',
                    ],
                    400
                );
            }

            // TODO:: jika check passed, kurangi stok bahan baku setelah diproses
            // ...

            // ambil status 'Pesanan diproses' (just in case)
            $statusRes = StatusTransaksi::query()
                ->where('nama_status', 'like', '%diproses%')
                ->first();

            // ubah status transaksi menjadi diproses
            $transaksiUpdated = Transaksi::find($found->id);
            $transaksiUpdated->status_transaksi_id = $statusRes->id;
            $transaksiUpdated->save();

            return response()->json(
                [
                    'data' => $transaksiUpdated,
                    'message' => 'Berhasil proses transaksi.'
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

    public function prosesSemuaTransaksi()
    {
        try {
            $res = $this->getArrPesananHarian();
            $transaksiCollection =  collect($res['transaksi_arr']);

            if (!$transaksiCollection) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
                    ],
                    400
                );
            }

            // TODO:: check warning bahan baku (dari transaksi ARRAY)
            // ...
            $checked = true;

            if (!$checked) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak dapat diproses karena ada bahan baku yang kurang.',
                    ],
                    400
                );
            }

            // TODO:: jika check passed, kurangi stok bahan baku setelah diproses (dari transaksi ARRAY)
            // ...

            // ambil status 'Pesanan diproses' (just in case)
            $statusRes = StatusTransaksi::query()
                ->where('nama_status', 'like', '%diproses%')
                ->first();

            // ubah status transaksi menjadi diproses (dari transaksi ARRAY)
            $transaksiCollectionUpdated = $transaksiCollection->map(function ($item) use ($statusRes) {
                $transaksiUpdated = Transaksi::find($item->id);
                $transaksiUpdated->status_transaksi_id = $statusRes->id;
                $transaksiUpdated->save();
            });

            return response()->json(
                [
                    'data' => $transaksiCollectionUpdated,
                    'message' => 'Berhasil proses transaksi.'
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

    // list pesanan harian ---> hanya untuk tampilan di web saja, list transaksi & produk & bahan baku yg dibutuhkan
    public function index()
    {
        try {
            $res = $this->getArrPesananHarian();

            // list pesanan harian yang perlu diproses hari ini
            // hari ini diproses === h-1 tanggal ambil
            // ambil yang hari ini = tanggal ambil - 1
            // alias hari ini + 1 = tanggal ambil
            $today = $res['today'];
            $todayPlusOne = $res['today_plus_one'];

            // ambil transaksi yang sesuai tanggal & status transaksi = Pesanan diterima
            $transaksiArr =  $res['transaksi_arr'];

            // LIST PESANAN

            // hitung bahan baku
            // 1. bahan baku yang dihitung hanya untuk produk/hampers yg status belinya PRE ORDER
            // 2. porsi yg diorder direkap untuk setiap produk, misal 3 *0.5 + 2 * 1 = 3.5 Loyang Produk A
            // 3. jika porsi setelah direkap <= 1 (misal cuma beli 1/2), maka yg dibuat tetap 1 Loyang minimal **cek kapasitas minimum mesin 

            // -------------------------------------------------------------------------------------------------------------------------------------

            // 1 - cari transaksi yang PRE ORDER aja (karena kalo udah READY STOCK gaperlu diproses/dimasak)

            // 1.1 - semua produk yang ada di transaksi dikumpulkan menjadi satu 
            $allProduksTransaksi = [];
            $newArr = [];
            $statusSearched = 'Pre Order';

            foreach ($transaksiArr as $value) {
                $detailCarts = $value->cart->detailCart;
                foreach ($detailCarts as $detailItem) {
                    if (isset($detailItem->produk)) {
                        if ($detailItem->status_produk === $statusSearched) {
                            $newArr = $detailItem['produk'];
                            // $newArr['id'] = $detailItem['produk']['id'];
                            $newArr['jumlah_dibeli'] = $detailItem['jumlah'];
                            $allProduksTransaksi[] = $newArr;
                        }
                    } else if (isset($detailItem->hampers)) {
                        foreach ($detailItem->hampers->detailHampers as $detailHampers) {
                            if ($detailItem->status_produk === $statusSearched) {
                                $newArr = $detailHampers['produk'];
                                // $newArr['id'] = $detailHampers['produk']['id'];
                                $newArr['jumlah_dibeli'] = $detailItem['jumlah'] * $detailHampers['jumlah_produk'];
                                $allProduksTransaksi[] = $newArr;
                            }
                        }
                    }
                }
            }

            // 1.2 - rekap per produk, group by id produk supaya unik, sum jumlah produk yang dibeli
            $allProduksTransaksiCollection = collect($allProduksTransaksi);
            $allProduksTransaksiGrouped = $allProduksTransaksiCollection->groupBy('id');
            $allProduksTransaksiResults = $allProduksTransaksiGrouped->map(function ($group) {
                $item = $group->first();
                $item['jumlah_dibeli'] = $group->sum('jumlah_dibeli');
                return $item;
            })->values();


            // 2 - rekap porsi per produk (nama produk unik)

            // 2.1 - buat collection isinya nama produk unik / tanpa ada porsi loyang di nama produk
            $namaProdukCakeUniqueCollection = collect(
                [
                    'Lapis Legit',
                    'Lapis Surabaya',
                    'Brownies',
                    'Spikoe',
                ]
            );

            // 2.2 - search/compare nama produk, searched === true semisal ditemukan 'Lapis Legit' pada 'Lapis Legit 1/2 Loyang', dst..
            //       ketika searched === true, ubah nama produk menjadi 'Lapis Legit', dst..
            $allPesananYangPerluDibuat = $allProduksTransaksiResults->map(function ($item) use ($namaProdukCakeUniqueCollection) {
                $namaProduk = $item->nama_produk;
                $foundIndex = $namaProdukCakeUniqueCollection->search(function ($elFromCollection) use ($namaProduk) {
                    return strpos($namaProduk, $elFromCollection) !== false;
                });

                // jika nama produk ketemu di nama produk unique collection, ubah nama produk jadi nama unique supaya nanti bisa direkap porsinya
                if ($foundIndex !== false) {
                    $itemCopy = clone $item;
                    $itemCopy->nama_produk = $namaProdukCakeUniqueCollection[$foundIndex];
                    return $itemCopy;
                }
                return $item;
            });

            // 2.3 - group by nama produk unik tadi, lalu cari rekap porsi yang perlu dibuat
            $allPesananYangPerluDibuatGrouped = $allPesananYangPerluDibuat->groupBy('nama_produk');
            $allPesananYangPerluDibuatResults = $allPesananYangPerluDibuatGrouped->map(function ($group) {
                $item = $group->first();
                if ($item->porsi !== null) {
                    // untuk mencari jumlah total porsi yang perlu dibuat, sum (jumlah dibeli * porsi masing masing)
                    $item['jumlah_porsi_yang_perlu_dibuat'] = $group->sum(function ($groupSumItem) {
                        // 3 - bila jumlah total porsi yang perlu dibuat masih ada yang 1/2 (atau <= 1 lah pokoknya), porsi yang perlu dibuat tetap dianggap 1
                        //     karena mesin minimal membuat 1 porsi loyang
                        $sum = $groupSumItem->jumlah_dibeli * $groupSumItem->porsi;
                        if ($sum <= 1) {
                            $sum = 1;
                        }
                        return $sum;
                    });
                }
                return $item;
            })->values();

            // bahan baku tapi belum digabungin
            $allProduksWithResep = $allProduksTransaksiResults->map(function ($it) {
                $jmlDibeli = $it->jumlah_dibeli;
                if ($it->resep) {
                    $it['resep'] = $it->resep->detailResep->map(function ($dr) use ($jmlDibeli) {
                        $dr['jumlah_dibeli_x_jumlah_bahan_resep'] = $dr->jumlah_bahan_resep * $jmlDibeli;
                        $dr['bahan_baku'] = $dr->bahanBaku;
                        return $dr;
                    });
                }
                return $it;
            });

            // LIST BAHAN BAKU

            // // {SEMENTARA TEST RETURN}
            // return response()->json(
            //     [
            //         'data' => $allProduksWithResep,
            //         'message' => 'Berhasil ambil data list pesanan harian yang perlu diproses.'
            //     ],
            //     200
            // );



            // GET DATA

            $data = [];
            $data['list_pesanan'] = $transaksiArr;
            $data['rekap_pesanan'] = $allProduksTransaksiResults;
            $data['pesanan_yang_perlu_dibuat'] = $allPesananYangPerluDibuatResults;
            $data['tanggal_sekarang'] = $today;
            $data['tanggal_ambil_dicek'] = $todayPlusOne;

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Berhasil ambil data list pesanan harian yang perlu diproses.'
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
