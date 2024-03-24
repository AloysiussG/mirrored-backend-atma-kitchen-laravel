<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promo_points')->insert([
        [
            'jumlah_kelipatan_bayar' => 10000,
            'jumlah_poin_diterima' => 1,
        ],
        [
            'jumlah_kelipatan_bayar' => 100000,
            'jumlah_poin_diterima' => 15,
        ],
        [
            'jumlah_kelipatan_bayar' => 500000,
            'jumlah_poin_diterima' => 75,
        ],
        [
            'jumlah_kelipatan_bayar' => 1000000,
            'jumlah_poin_diterima' => 200,
        ],
        ]);
    }
}
