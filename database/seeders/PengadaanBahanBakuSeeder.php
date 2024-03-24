<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'jumlah_pengadaan' => 1000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-01-01',
            ],
            [
                'bahan_baku_id' => 2,
                'jumlah_pengadaan' => 1000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-01-01',
            ],
            [
                'bahan_baku_id' => 3,
                'jumlah_pengadaan' => 100,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'butir',
                'tanggal_pengadaan' => '2024-01-01',
            ],
            [
                'bahan_baku_id' => 4,
                'jumlah_pengadaan' => 100,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-01-02',
            ],
            [
                'bahan_baku_id' => 5,
                'jumlah_pengadaan' => 200,
                'harga_pengadaan_bahan_baku' => 200000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-01-02',
            ],
            [
                'bahan_baku_id' => 6,
                'jumlah_bahan' => 20000,
                'harga_pengadaan_bahan_baku' => 250000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-01-02',
            ],
            [
                'bahan_baku_id' => 7,
                'jumlah_pengadaan' => 10000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-02',
            ],
            [
                'bahan_baku_id' => 8,
                'jumlah_pengadaan' => 1000,
                'harga_pengadaan_bahan_baku' => 100000,
                'satuan_pengadaan' => 'gram',
                'tanggal_pengadaan' => '2024-02-02',
            ],
        ]);
    }
}
