<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DetailCart;
use App\Models\DetailHampers;
use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Presensi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Carbon\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LaporanSamController extends Controller
{
    public function laporan_presensi(Request $request)
    {
        try {
            // $laporan_presensi = [];

            // $karyawan = Karyawan::all();
            // foreach ($karyawan as $k) {
            //     $laporan_presensi[] = [
            //         'karyawan_id' => $k->id,
            //         'nama' => $k->nama,
            //     ];
            //     $presensi = Presensi::all();
            //     $presensi1 = [];
            //     foreach ($presensi as $p) {
            //         $laporan_presensi = collect($laporan_presensi);
            //         $index = $laporan_presensi->first(function ($item) use ($p) {
            //             return $item['karyawan_id'] == $p->karyawan_id;
            //         });
            //         if($index){
            //             $presensi1[] = [
            //                 'karyawan_id' => $p->karyawan_id,
            //                 'tanggal_bolos' => $p->tanggal_bolos,
            //             ];
            //         }
            //         $laporan_presensi = $laporan_presensi->toArray();
            //         $laporan_presensi['presensi'] = $presensi1;
            //         echo json_encode($laporan_presensi);
            //     }
            // }


            // $laporan_presensi = DB::table('presensis as p')
            //     ->join('karyawans as k', 'p.karyawan_id', '=', 'k.id')
            //     ->selectRaw('MONTH(p.tanggal_bolos) as month, count(p.tanggal_bolos) as count, k.id as karyawan_id')
            //     ->groupBy(DB::raw('MONTH(p.tanggal_bolos)'), 'k.id')
            //     ->get();

            if($request->date){
                $currMonth = Carbon::parse($request->date)->month;
                $currYear = Carbon::parse($request->date)->year;
            }else{
                $currMonth = Carbon::now()->month;
                $currYear = Carbon::now()->year;
            }

            $currdate = Carbon::now();
            // $currMonth = $currdate->month;
            // $currMonth = 2; //buat cek aja biar byk datanya
            // $currYear = Carbon::now()->year;




            $currDateFormatted = $currdate->toDateString();
            // echo $currMonth;
            $presensi_in_month = Presensi::query()->whereYear('tanggal_bolos', $currYear)->whereMonth('tanggal_bolos', $currMonth)->select('karyawan_id', DB::raw('COUNT(karyawan_id) as jumlah_bolos'))->groupBy('karyawan_id')->get();

            foreach ($presensi_in_month as &$p) {
                $days_in_month = Carbon::createFromDate(Carbon::now()->year, $currMonth, 1)->daysInMonth();
                $p->jumlah_masuk = $days_in_month - $p->jumlah_bolos;
                if($p->jumlah_bolos <= 4){
                    $p->bonus = $p->karyawan->bonus_gaji;
                }
                $p->karyawan = Karyawan::find($p->karyawan_id);
                $p->month = $currMonth;
                $p->year = $currYear;
                $p->date = $currDateFormatted;
                if($p->bonus){
                    $p->total_gaji = $p->karyawan->gaji * $p->jumlah_masuk + $p->bonus;
                }else{
                    $p->total_gaji = $p->karyawan->gaji * $p->jumlah_masuk;
                }
            }

            $message = [
                'month' => $currMonth,
                'year' => $currYear,
                'date' => $currDateFormatted,
            ];

            return response()->json([
                'message' => $message,
                'data' => $presensi_in_month,
            ]);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    public function laporan_penitip(Request $request)
    {
        try {

            if($request->date){
                $currMonth = Carbon::parse($request->date)->month;
                $currYear = Carbon::parse($request->date)->year;
            }else{
                $currMonth = Carbon::now()->month;
                $currYear = Carbon::now()->year;
            }

            // $currYear = Carbon::now()->year;
            $rekap = [];
            // $month = 2; //buat cek aja biar byk datanya
            // $month = Carbon::now()->month;
            $laporan_penitip = Transaksi::query()->whereYear('tanggal_lunas', $currYear)->whereMonth('tanggal_lunas', $currMonth)->get();

            foreach ($laporan_penitip as $l) {
                $cart = $l->cart_id;
                $detail_carts = DetailCart::query()->where('cart_id', $cart)->get();

                foreach ($detail_carts as $d) {
                    if ($d->produk) {
                        if ($d->produk->penitip) {
                            $found = false;
                            foreach ($rekap as &$r) {
                                if ($r['nama_penitip'] == $d->produk->penitip->nama_penitip) {
                                    $found = true;
                                    $produkFound = false;
                                    foreach ($r['produk'] as &$p) {
                                        if ($p['nama_produk'] == $d->produk->nama_produk) {
                                            $p['kuantitas'] += $d->jumlah;
                                            $p['harga'] = $d->harga_produk_sekarang;
                                            $p['jumlah_penjualan'] += $d->jumlah * $d->harga_produk_sekarang;
                                            $produkFound = true;
                                        }
                                    }
                                    if (!$produkFound) {
                                        $r['produk'][] = [
                                            "nama_produk" => $d->produk->nama_produk,
                                            "kuantitas" => $d->jumlah,
                                            "harga" => $d->harga_produk_sekarang,
                                            "jumlah_penjualan" => $d->jumlah * $d->harga_produk_sekarang
                                        ];
                                    }
                                }
                            }
                            if (!$found) {
                                $rekap[] = [
                                    "id" => $d->produk->penitip->id,
                                    "tahun" => $currYear,
                                    "bulan" => $currMonth, //ini buat cek aja biar byk datanya
                                    "nama_penitip" => $d->produk->penitip->nama_penitip,
                                    "produk" => [
                                        [
                                            "nama_produk" => $d->produk->nama_produk,
                                            "kuantitas" => $d->jumlah,
                                            "harga" => $d->harga_produk_sekarang,
                                            "jumlah_penjualan" => $d->jumlah * $d->harga_produk_sekarang
                                        ]
                                    ]
                                ];
                            }

                        }
                    } else if ($d->hampers) {
                        $dh = DetailHampers::query()->where('hampers_id', $d->hampers_id)->get();
                        foreach ($dh as $detil) {
                            if ($detil->produk && $detil->produk->penitip) {
                                $found = false;
                                foreach ($rekap as &$r) {
                                    if ($r['nama_penitip'] == $detil->produk->penitip->nama_penitip) {
                                        $found = true;
                                        $produkFound = false;
                                        foreach ($r['produk'] as &$p) {
                                            if ($p['nama_produk'] == $detil->produk->nama_produk) {
                                                $p['kuantitas'] += $d->jumlah;
                                                $p['harga'] = $d->harga_produk_sekarang;
                                                $p['jumlah_penjualan'] += $d->jumlah * $d->harga_produk_sekarang;
                                                $produkFound = true;
                                            }
                                        }
                                        if (!$produkFound) {
                                            $r['produk'][] = [
                                                "nama_produk" => $detil->produk->nama_produk,
                                                "kuantitas" => $d->jumlah,
                                                "harga" => $d->harga_produk_sekarang,
                                                "jumlah_penjualan" => $d->jumlah * $d->harga_produk_sekarang
                                            ];
                                        }
                                    }
                                }
                                if (!$found) {
                                    $rekap[] = [
                                        "id" => $detil->produk->penitip->id,
                                        "tahun" => $currYear,
                                        "bulan" => $currMonth, //ini buat cek aja biar byk datanya
                                        "nama_penitip" => $detil->produk->penitip->nama_penitip,
                                        "produk" => [
                                            [
                                                "nama_produk" => $detil->produk->nama_produk,
                                                "kuantitas" => $d->jumlah,
                                                "harga" => $d->harga_produk_sekarang,
                                                "jumlah_penjualan" => $d->jumlah * $d->harga_produk_sekarang
                                            ]
                                        ]
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            $currDateFormatted = Carbon::now()->toDateString();

            $message = [
                'month' => $currMonth,
                'year' => $currYear,
                'date' => $currDateFormatted,
            ];

            return response()->json([
                'message' => $message,
                'data' => $rekap,
            ]);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }


    public function laporanPengeluaranPemasukan(Request $request)
    {
        try {
            if($request->date){
                $currMonth = Carbon::parse($request->date)->month;
                $currYear = Carbon::parse($request->date)->year;
            }else{
                $currMonth = Carbon::now()->month;
                $currYear = Carbon::now()->year;

            }
            $pemasukan = Transaksi::query()->whereYear('tanggal_lunas',$currYear)->whereMonth('tanggal_lunas', $currMonth)->get();
            $total_pemasukan = 0;
            $total_tip = 0;
            foreach ($pemasukan as $p) {
                $total_pemasukan += $p->total_harga;
                $total_tip += $p->tip;
            }

            $pengeluaran = Pengeluaran::query()->whereYear('tanggal_pengeluaran',$currYear)->whereMonth('tanggal_pengeluaran', $currMonth)->get();
            $total_pengeluaran = 0;
            foreach ($pengeluaran as $p) {
                $p->tipe = 'pengeluaran';
                $total_pengeluaran += $p->total_pengeluaran;
            }


            $transaksi = [
                'jenis' => "transaksi",
                'jumlah' => $total_pemasukan,
                'tipe' => 'pemasukan'
            ];

            $tip = [
                'jenis' => "tip",
                'jumlah' => $total_tip,
                'tipe' => 'pemasukan'
            ];


            $pengeluaran[] = $tip;
            $pengeluaran[] = $transaksi;

            $rekap = [];

            $rekap = [
                "pemasukan" => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'total_pengeluaran' => $total_pengeluaran,
            ];
            $currDateFormatted = Carbon::now()->toDateString();
            $message = [
                'month' => $currMonth,
                'year' => $currYear,
                'date' => $currDateFormatted,
            ];

            return response()->json([
                'message' => $message,
                'data' => $pengeluaran,
            ]);
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }
}
