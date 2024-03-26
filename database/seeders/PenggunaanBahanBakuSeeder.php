<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class PenggunaanBahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penggunaan_bahan_bakus')->insert([
            [
                'bahan_baku_id' => 1,
                'jumlah_penggunaan' => 500,
                'satuan_penggunaan' => 'gram', 
                'tanggal_penggunaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'butir', 
                'tanggal_penggunaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 20,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-02-01',
            ],
            [
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 500,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 4,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 7,
                'jumlah_bahan_resep' => 10,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 8,
                'jumlah_bahan_resep' => 25,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 9,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
            [
                'bahan_baku_id' => 16,
                'jumlah_bahan_resep' => 250,
                'satuan_detail_resep' => 'gram', 
                'tanggal_penggunaan' => '2024-03-01',
            ],
        ]);
    }
}
