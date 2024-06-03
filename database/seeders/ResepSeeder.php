<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('reseps')->truncate();
        DB::table('reseps')->insert([
            // NEW RESEP WITH FK TO PRODUKUNIQUE
            [
                'produk_id' => 1,
                'nama_resep' => 'Lah-pees Leh-zheet Andalan',
            ],
            [
                'produk_id' => 2,
                'nama_resep' => 'Lapis Surabaya ala Chef Wilson',
            ],
            [
                'produk_id' => 3,
                'nama_resep' => 'Fudgy Brownies by Ramsey',
            ],
            [
                'produk_id' => 4,
                'nama_resep' => 'Spikoe by Arnoldpo',
            ],
            [
                'produk_id' => 5,
                'nama_resep' => 'Roti Sosis',
            ],
            [
                'produk_id' => 6,
                'nama_resep' => 'Milk Bun',
            ],
            [
                'produk_id' => 7,
                'nama_resep' => 'Matcha Creamy Latte',
            ],
            [
                'produk_id' => 10,
                'nama_resep' => 'Mandarin',
            ],
            [
                'produk_id' => 11,
                'nama_resep' => 'Roti Keju',
            ],
            [
                'produk_id' => 12,
                'nama_resep' => 'Choco Creamy Latte',
            ],

            // // OLD RESEP WITH FK TO PRODUK
            // [
            //     'produk_id' => 1,
            //     'nama_resep' => 'Lah-pees Leh-zheet Andalan',
            // ],
            // [
            //     'produk_id' => 3,
            //     'nama_resep' => 'Lapis Surabaya ala Chef Wilson',
            // ],
            // [
            //     'produk_id' => 5,
            //     'nama_resep' => 'Fudgy Brownies by Ramsey',
            // ],
        ]);
    }
}
