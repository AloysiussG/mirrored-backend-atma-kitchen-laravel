<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StatusTransaksi;
use App\Models\Transaksi;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class PemrosesanPesananController extends Controller
{
    /**
     * CUSTOM FUNCTIONS
     */
    public function getArrPesananHarian($search = '')
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
        $transaksiArrQuery = Transaksi::query()
            ->with(['cart.customer', 'cart.detailCart.produk.kategoriProduk', 'cart.detailCart.hampers.detailHampers.produk.kategoriProduk'])
            ->whereDate('tanggal_ambil', $todayPlusOne)
            ->where('status_transaksi_id', $statusRes->id);

        if ($search) {
            $transaksiArrQuery->where('no_nota', 'like', '%' . $search . '%');
        }

        $transaksiArr = $transaksiArrQuery->orderBy('id', 'asc')->get();

        return [
            'today' => $today,
            'today_plus_one' => $todayPlusOne,
            'transaksi_arr' => $transaksiArr,
        ];
    }

    public function checkPesananHarian($transaksiArr)
    {
        if (count($transaksiArr) <= 0) {
            return [];
        }

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
        })->sortBy('nama_produk')->values();

        // 2 - rekap porsi per produk (nama produk unik)

        // 2.2 - search/compare nama produk, searched === true semisal ditemukan 'Lapis Legit' pada 'Lapis Legit 1/2 Loyang', dst..
        //       ketika searched === true, ubah nama produk menjadi 'Lapis Legit', dst..
        $allPesananYangPerluDibuat = $allProduksTransaksiResults->map(function ($item) {
            $namaProduk = $item->produkUnique->nama_produk;
            if (!$namaProduk) {
                $namaProduk = $item->nama_produk;
            }
            $itemCopy = clone $item;
            $itemCopy->nama_produk = $namaProduk;
            return $itemCopy;
        });

        // 2.3 - group by nama produk unik tadi, lalu cari rekap porsi yang perlu dibuat
        $allPesananYangPerluDibuatGrouped = $allPesananYangPerluDibuat->groupBy('nama_produk');
        $allPesananYangPerluDibuatResults = $allPesananYangPerluDibuatGrouped->map(function ($group) {
            $item = $group->first();
            if ($item->porsi !== null) {
                // untuk mencari jumlah total porsi yang perlu dibuat, sum (jumlah dibeli * porsi masing masing)
                $jmlPorsiSum = $group->sum(function ($groupSumItem) {
                    $sum = $groupSumItem->jumlah_dibeli * $groupSumItem->porsi;
                    return $sum;
                });
                // 3 - bila jumlah total porsi yang perlu dibuat masih ada yang 1/2 (atau <= 1 lah pokoknya), porsi yang perlu dibuat tetap dianggap 1
                //     karena mesin minimal membuat 1 porsi loyang
                if ($jmlPorsiSum < 1) {
                    $item['is_kurang_dari_1_loyang'] = 'true';
                    // cari sisa porsi yang tidak dibeli,
                    // semisal jmlPorsiSum yang dibeli 1/2 loyang, tetep buat 1 loyang
                    // nah, 1/2 sisanya akan dikembalikan ke stok produk
                    $sisaPorsiYgTidakDibeli = 1 - $jmlPorsiSum;
                    $produkInSameProdukUnique = $item->produkUnique->produks->firstWhere('porsi', '<=', $sisaPorsiYgTidakDibeli);
                    if ($produkInSameProdukUnique) {
                        $item['sisa_produk_yang_tidak_dibeli'] = $produkInSameProdukUnique;
                        $item['sisa_produk_yang_tidak_dibeli']['jumlah'] = 1;
                    }
                    $item['jumlah_porsi_awal'] = $jmlPorsiSum;
                    $item['jumlah_porsi_sisa'] = $sisaPorsiYgTidakDibeli;
                    $jmlPorsiSum = 1;
                }
                $item['jumlah_porsi_yang_perlu_dibuat'] = $jmlPorsiSum;
            }
            return $item;
        })->values();

        // LIST BAHAN BAKU
        $allProduksWithResep = $allPesananYangPerluDibuatResults->map(function ($it) {
            $jmlPorsiTotal = $it->jumlah_porsi_yang_perlu_dibuat;
            // jika bukan produk yang berporsi, pake jumlah_dibeli
            if (!$jmlPorsiTotal) {
                $jmlPorsiTotal = $it->jumlah_dibeli;
            }
            if ($it->produkUnique->resep) {
                $produkWithResep['nama_produk'] = $it->produkUnique->nama_produk;
                $produkWithResep['detail_resep'] = $it->produkUnique->resep->detailResep->map(function ($dr) use ($jmlPorsiTotal) {
                    $new['bahan_baku_id'] = $dr->bahanBaku->id;
                    $new['nama_bahan_baku'] = $dr->bahanBaku->nama_bahan_baku;
                    $new['satuan_bahan'] = $dr->bahanBaku->satuan_bahan;
                    $new['jml_porsi_total'] = $jmlPorsiTotal;
                    $new['jumlah_bahan_resep'] = $dr->jumlah_bahan_resep;
                    $new['jml_porsi_total_x_jumlah_bahan_resep'] = $dr->jumlah_bahan_resep * $jmlPorsiTotal;
                    return $new;
                });
                return $produkWithResep;
            }
        })->filter(fn ($item) => $item != null);

        $allDetailResep = $allProduksWithResep->flatMap(fn ($item) => $item['detail_resep']);

        $allDetailResepGrouped = $allDetailResep->groupBy('bahan_baku_id');
        $allDetailResepGroupedResults = $allDetailResepGrouped->map(function ($group) {
            $item['bahan_baku_id'] = $group->first()['bahan_baku_id'];
            $item['nama_bahan_baku'] = $group->first()['nama_bahan_baku'];
            $item['satuan_bahan'] = $group->first()['satuan_bahan'];
            $item['sum_jumlah_bahan'] = $group->sum('jml_porsi_total_x_jumlah_bahan_resep');
            return $item;
        })->sortBy('nama_bahan_baku')->values();

        // WARNINGS
        $warnings = 0;

        // WARNING BAHAN BAKU
        $allDetailResepGroupedResultsWithWarning = $allDetailResepGroupedResults->map(function ($item) use (&$warnings) {
            $bahanBaku = BahanBaku::find($item['bahan_baku_id']);
            $item['stok_saat_ini'] = $bahanBaku->jumlah_bahan_baku;
            if ($bahanBaku->jumlah_bahan_baku < $item['sum_jumlah_bahan']) {
                $item['warning'] = 'true';
                $warnings += 1;
            }
            return $item;
        });

        // LIST PACKAGING PER TRANSAKSI
        $newTransaksiArr = $transaksiArr->map(function ($value) {
            $detailCarts = $value->cart->detailCart;
            $listPackaging = [];
            $newData = [];
            foreach ($detailCarts as $detailItem) {
                if (isset($detailItem->produk)) {
                    foreach ($detailItem->produk->packagings as $pkg) {
                        $newData['bahan_baku_id'] = $pkg->bahan_baku_id;
                        $newData['nama_bahan_baku'] = $pkg->bahanBaku->nama_bahan_baku;
                        $newData['jumlah_packaging'] = $pkg->jumlah * $detailItem->jumlah;
                        $listPackaging[] = $newData;
                    }
                } else if (isset($detailItem->hampers)) {
                    foreach ($detailItem->hampers->packagings as $pkg) {
                        $newData['bahan_baku_id'] = $pkg->bahan_baku_id;
                        $newData['nama_bahan_baku'] = $pkg->bahanBaku->nama_bahan_baku;
                        $newData['jumlah_packaging'] = $pkg->jumlah * $detailItem->jumlah;
                        $listPackaging[] = $newData;
                    }
                    foreach ($detailItem->hampers->detailHampers as $detailHampers) {
                        foreach ($detailHampers->produk->packagings as $pkg) {
                            $newData['bahan_baku_id'] = $pkg->bahan_baku_id;
                            $newData['nama_bahan_baku'] = $pkg->bahanBaku->nama_bahan_baku;
                            $newData['jumlah_packaging'] = $pkg->jumlah * $detailItem->jumlah;
                            $listPackaging[] = $newData;
                        }
                    }
                }
            }
            // + 1 x tas spunbond
            $packagingTransaksi = BahanBaku::query()
                ->where('nama_bahan_baku', 'like', 'tas spunbond')
                ->first();
            $listPackaging[] = [
                'bahan_baku_id' => $packagingTransaksi->id,
                'nama_bahan_baku' => $packagingTransaksi->nama_bahan_baku,
                'jumlah_packaging' => 1,
            ];
            $listPackagingCollection = collect($listPackaging);
            $listPackagingCollectionResults = $listPackagingCollection->groupBy('bahan_baku_id')->map(function ($group) {
                $item = $group->first();
                $item['jumlah_packaging'] = $group->sum('jumlah_packaging');
                return $item;
            })->sortBy('nama_bahan_baku')->values();

            $value['list_packaging'] = $listPackagingCollectionResults;
            return $value;
        });

        // rekap bahan packaging
        $rekapBahanPackaging = $newTransaksiArr->flatMap(fn ($item) => $item['list_packaging']);
        $rekapBahanPackagingResults = $rekapBahanPackaging->groupBy('bahan_baku_id')->map(function ($group) {
            $item = $group->first();
            $item['jumlah_packaging'] = $group->sum('jumlah_packaging');
            return $item;
        })->sortBy('nama_bahan_baku')->values();

        // warning packaging
        $rekapBahanPackagingResultsWithWarning = $rekapBahanPackagingResults->map(function ($item) use (&$warnings) {
            $bahanBaku = BahanBaku::find($item['bahan_baku_id']);
            $item['stok_saat_ini'] = $bahanBaku->jumlah_bahan_baku;
            if ($bahanBaku->jumlah_bahan_baku < $item['jumlah_packaging']) {
                $item['warning'] = 'true';
                $warnings += 1;
            }
            return $item;
        });

        // GET DATA
        $data = [];
        $data['list_pesanan'] = $newTransaksiArr;
        $data['rekap_pesanan'] = $allProduksTransaksiResults;
        $data['pesanan_yang_perlu_dibuat'] = $allPesananYangPerluDibuatResults;
        $data['list_bahan_per_produk'] = $allProduksWithResep;
        $data['rekap_bahan'] = $allDetailResepGroupedResultsWithWarning;
        $data['rekap_bahan_packaging'] = $rekapBahanPackagingResultsWithWarning;
        $data['warnings_count'] = $warnings;

        return $data;
    }

    public function addPenggunaanByRekapBahan($data)
    {
        // TODO:: jika check passed, add penggunaan bahan baku
        // ...

        // TODO:: jika check passed, kurangi stok bahan baku setelah diproses
        // ...

        // TODO:: jika check passed, tambah stok produk sisa (misal ada pembelian 1/2 Loyang, bikin 1 Loyang, sisa 1/2 nya masuk stok produk 1/2 loyang)
        // ...

        $penggunaanController = new PenggunaanBahanBakuController();

        // add pengadaan bahan biasa
        if (count($data['rekap_bahan']) > 0) {
            $rekap = collect($data['rekap_bahan']);
            $rekap->map(function ($item) use ($penggunaanController) {
                $bb = BahanBaku::find($item['bahan_baku_id']);
                $penggunaanController->addPenggunaan([
                    'tanggal_penggunaan' => Carbon::now(),
                    'bahan_baku_id' => $item['bahan_baku_id'],
                    'jumlah_penggunaan' => $item['sum_jumlah_bahan'],
                    'satuan_penggunaan' => $bb->satuan_bahan,
                ]);
            });
        }

        // add pengadaan bahan packaging
        if (count($data['rekap_bahan_packaging']) > 0) {
            $rekap = collect($data['rekap_bahan_packaging']);
            $rekap->map(function ($item) use ($penggunaanController) {
                $bb = BahanBaku::find($item['bahan_baku_id']);
                $penggunaanController->addPenggunaan([
                    'tanggal_penggunaan' => Carbon::now(),
                    'bahan_baku_id' => $item['bahan_baku_id'],
                    'jumlah_penggunaan' => $item['jumlah_packaging'],
                    'satuan_penggunaan' => $bb->satuan_bahan,
                ]);
            });
        }

        // KEMBALIKAN STOK PRODUK
        // jika ada produk sisa beli < 1 loyang
        // misal beli 1/2 loyang tapi tetep diproses 1 loyang, 1/2 sisanya akan masuk ke stok produk
        $collectPesananPerluDibuat = collect($data['pesanan_yang_perlu_dibuat']);
        $collectPesananPerluDibuat->map(function ($item) {
            if ($item['is_kurang_dari_1_loyang']) {
                $idProdukKembali = $item['sisa_produk_yang_tidak_dibeli']['id'];
                $jmlKembali = $item['sisa_produk_yang_tidak_dibeli']['jumlah'];
                // kembalikan ke stok produk
                $produk = Produk::find($idProdukKembali);
                $produk->jumlah_stock = $produk->jumlah_stock + $jmlKembali;
                $produk->status = 'Ready Stock';
                $produk->save();
            }
        });
    }

    /**
     * API CONTROLLER FUNCTIONS
     */

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

    public function cekProsesTransaksi(string $id)
    {
        try {
            $res = $this->getArrPesananHarian();
            $transaksiCollection =  collect($res['transaksi_arr']);
            $found = $transaksiCollection->where('id', $id)->values();

            // cek apakah id transaksi dari URL ada di dalam list transaksi hari ini, just in case
            if (!count($found)) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
                    ],
                    400
                );
            }

            $data = $this->checkPesananHarian($found);

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Berhasil cek proses transaksi.'
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
            $found = $transaksiCollection->where('id', $id)->values();



            // cek apakah id transaksi dari URL ada di dalam list transaksi hari ini, just in case
            if (!count($found)) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
                    ],
                    400
                );
            }

            // TODO:: check warning bahan baku
            $data = $this->checkPesananHarian($found);

            if ($data['warnings_count'] > 0) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak dapat diproses karena ada bahan baku yang kurang.',
                    ],
                    400
                );
            }

            // // FOUND
            // return response()->json(
            //     [
            //         'data' => $data,
            //         'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
            //     ],
            //     400
            // );

            // TODO:: jika check passed, add penggunaan bahan baku
            // ...

            // TODO:: jika check passed, kurangi stok bahan baku setelah diproses
            // ...

            // TODO:: jika check passed, tambah stok produk sisa (misal ada pembelian 1/2 Loyang, bikin 1 Loyang, sisa 1/2 nya masuk stok produk 1/2 loyang)
            // ...

            $this->addPenggunaanByRekapBahan($data);

            // ambil status 'Pesanan diproses' (just in case)
            $statusRes = StatusTransaksi::query()
                ->where('nama_status', 'like', '%diproses%')
                ->first();

            // ubah status transaksi menjadi diproses
            $foundFirst = $found->first();
            $transaksiUpdated = Transaksi::find($foundFirst->id);
            $transaksiUpdated->status_transaksi_id = $statusRes->id;
            $transaksiUpdated->save();
            $customer = $transaksiUpdated->cart->customer;
            $title = 'Status Transaksi Diperbaharui';
            $body = $transaksiUpdated->statusTransaksi->nama_status;
            $customer->notify(new PushNotification($title, $body));

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

    public function cekProsesSemuaTransaksi()
    {
        try {
            $res = $this->getArrPesananHarian();
            $transaksiCollection =  collect($res['transaksi_arr']);

            if (!count($transaksiCollection)) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
                    ],
                    400
                );
            }

            $data = $this->checkPesananHarian($transaksiCollection);

            return response()->json(
                [
                    'data' => $data,
                    'message' => 'Berhasil cek proses transaksi.'
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

            if (!count($transaksiCollection)) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Tidak ada transaksi yang perlu diproses di hari ini.',
                    ],
                    400
                );
            }

            if (!$transaksiCollection) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak ditemukan di dalam list transaksi yang perlu diproses hari ini.',
                    ],
                    400
                );
            }

            // TODO:: check warning bahan baku
            $data = $this->checkPesananHarian($transaksiCollection);

            if ($data['warnings_count'] > 0) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Transaksi tidak dapat diproses karena ada bahan baku yang kurang.',
                    ],
                    400
                );
            }

            // TODO:: jika check passed, add penggunaan bahan baku
            // ...

            // TODO:: jika check passed, kurangi stok bahan baku setelah diproses
            // ...

            // TODO:: jika check passed, tambah stok produk sisa (misal ada pembelian 1/2 Loyang, bikin 1 Loyang, sisa 1/2 nya masuk stok produk 1/2 loyang)
            // ...

            $this->addPenggunaanByRekapBahan($data);

            // ambil status 'Pesanan diproses' (just in case)
            $statusRes = StatusTransaksi::query()
                ->where('nama_status', 'like', '%diproses%')
                ->first();

            // ubah status transaksi menjadi diproses (dari transaksi ARRAY)
            $transaksiCollectionUpdated = $transaksiCollection->map(function ($item) use ($statusRes) {
                $transaksiUpdated = Transaksi::find($item->id);
                $transaksiUpdated->status_transaksi_id = $statusRes->id;
                $transaksiUpdated->save();
                $customer = $transaksiUpdated->cart->customer;
                $title = 'Status Transaksi Diperbaharui';
                $body = $transaksiUpdated->statusTransaksi->nama_status;
                $customer->notify(new PushNotification($title, $body));
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
    public function index(Request $request)
    {
        try {
            $res = $this->getArrPesananHarian($request->search);

            // list pesanan harian yang perlu diproses hari ini
            // hari ini diproses === h-1 tanggal ambil
            // ambil yang hari ini = tanggal ambil - 1
            // alias hari ini + 1 = tanggal ambil
            $today = $res['today'];
            $todayPlusOne = $res['today_plus_one'];

            // ambil transaksi yang sesuai tanggal & status transaksi = Pesanan diterima
            $transaksiArr =  $res['transaksi_arr'];

            $data = $this->checkPesananHarian($transaksiArr);
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
