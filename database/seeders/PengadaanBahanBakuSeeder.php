<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengadaanBahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengadaan_bahan_bakus')->insert([
            [
                'bahan_baku_id' => 1,
                'jumlah_bahan' => 1000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 2,
                'jumlah_bahan' => 1000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 3,
                'jumlah_bahan' => 100,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'butir',
                'tanggal_pengadaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 4,
                'jumlah_bahan' => 100,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-02',
            ],
            [
                'bahan_baku_id' => 5,
                'jumlah_bahan' => 200,
                'harga_pengadaan_bahan_baku' => 200000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-02',
            ],
            [
                'bahan_baku_id' => 6,
                'jumlah_bahan' => 20000,
                'harga_pengadaan_bahan_baku' => 250000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-02',
            ],
            [
                'bahan_baku_id' => 7,
                'jumlah_bahan' => 10000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-03-02',
            ],
            [
                'bahan_baku_id' => 8,
                'jumlah_bahan' => 1000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-03-02',
            ],
        ]);
    }
}
