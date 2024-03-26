<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailHampersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_hampers')->insert([
            //PAKET A
            [
                //id =  1
                'hampers_id' => 1,
                'produk_id' => 2,
                'jumlah_produk' => 1,
            ],
            [
                //id =  2
                'hampers_id' => 1,
                'produk_id' => 6,
                'jumlah_produk' => 1,
            ],
            //PAKET A

            //PAKET B
            [
                //id =  3
                'hampers_id' => 2,
                'produk_id' => 4,
                'jumlah_produk' => 1,
            ],
            [
                //id =  4
                'hampers_id' => 2,
                'produk_id' => 9,
                'jumlah_produk' => 1,
            ],
            //PAKET B

            //PAKET C
            [
                //id =  5
                'hampers_id' => 3,
                'produk_id' => 8,
                'jumlah_produk' => 1,
            ],
            [
                //id =  6
                'hampers_id' => 3,
                'produk_id' => 11,
                'jumlah_produk' => 1,
            ],
            //PAKET C
        ]);
    }
}
