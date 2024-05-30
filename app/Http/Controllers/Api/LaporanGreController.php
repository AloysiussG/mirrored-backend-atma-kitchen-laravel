<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PenggunaanBahanBaku;
use App\Models\Transaksi;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Validator;

class LaporanGreController extends Controller
{
    public function laporanPendapatan(String $tahun)
    {
        try {
            $transaksi = Transaksi::whereYear('tanggal_pesan', $tahun)
                ->where('status_transaksi_id', 11)
                ->get();

            if ($transaksi->isEmpty()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Tidak ada transaksi yang terselesaikan pada tahun ' . $tahun
                    ],
                    404
                );
            }

            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            $laporan = [];
            $laporan['tahun'] = $tahun;
            $laporan['tanggal_cetak'] = Carbon::now();
            $total = 0;
            for ($i = 1; $i <= 12; $i++) {
                $jumlahUang = 0;
                $jumlahTransaksi = 0;
                foreach ($transaksi as $item) {
                    $tanggal_pesan = new DateTime($item->tanggal_pesan);
                    if ($tanggal_pesan->format('m') == str_pad($i, 2, '0', STR_PAD_LEFT)) {
                        $jumlahUang += $item->total_harga;
                        $jumlahTransaksi++;
                    }
                }
                $total = $total + $jumlahUang;
                $laporan[] = [
                    'bulan' => $namaBulan[$i],
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'jumlah_uang' => $jumlahUang
                ];
            }
            $laporan['total'] = $total;

            return response(
                [
                    'message' => 'Laporan untuk tahun ' . $tahun . ' berhasil dibuat',
                    'data' => $laporan
                ],
                200
            );
        } catch (Throwable $e) {
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }


    public function laporanBahanBaku(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'tanggal_mulai' => 'required',
                'tanggal_akhir' => 'required',
            ], [
                'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
                'tanggal_akhir.required' => 'Tanggal akhir harus diisi',
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
            $penggunaan = PenggunaanBahanBaku::query()->with(['bahanBaku'])
                ->where('tanggal_penggunaan', '>=', $request->tanggal_mulai)
                ->where('tanggal_penggunaan', '<=', $request->tanggal_akhir)->get();

            if (!$penggunaan) {
                return response()->json(
                    [
                        'message' => 'Tidak ada penggunaan bahan baku pada rentang tanggal tersebut',
                        'data' => null
                    ],
                    404
                );
            }

            $laporan = [];
            $laporan['rentang_tanggal'] = $request->tanggal_mulai . ' - ' . $request->tanggal_akhir;
            $laporan['tanggal_cetak'] = Carbon::now();
            foreach($penggunaan as $item){
                $laporan[] = [
                    'nama_bahan_baku' => $item->bahanBaku->nama_bahan_baku,
                    'satuan_penggunaan' => $item->satuan_penggunaan,
                    'jumlah_penggunaan' => $item->jumlah_penggunaan,
                ];
            }

            return response()->json(
                [
                    'message' => 'Laporan penggunaan bahan baku berhasil dibuat',
                    'data' => $laporan
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
