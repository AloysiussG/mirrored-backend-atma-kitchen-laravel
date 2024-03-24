<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class DetailResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_reseps')->insert([
            [
                'resep_id' => 1,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 500,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'butir', 
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 300,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 4,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 20,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 500,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 40,
                'satuan_detail_resep' => 'butir', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 300,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 4,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 7,
                'jumlah_bahan_resep' => 10,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 8,
                'jumlah_bahan_resep' => 25,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 9,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 16,
                'jumlah_bahan_resep' => 250,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 100,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 15,
                'jumlah_bahan_resep' => 50,
                'satuan_detail_resep' => 'mililiter', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 6,
                'satuan_detail_resep' => 'butir', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 200,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 150,
                'satuan_detail_resep' => 'gram', 
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 8,
                'jumlah_bahan_resep' => 60,
                'satuan_detail_resep' => 'gram', 
            ],
            ]);
    }
}
