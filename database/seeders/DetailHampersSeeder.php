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
        DB::table('produks')->insert([
        //PAKET A
        [
            //id =  1
            'hampers_id' => 1,
            'produk_id' => 1,
            'jumlah_produk' => 0.5,
        ],
        [
            //id =  2
            'hampers_id' => 1,
            'produk_id' => 3,
            'jumlah_produk' => 0.5,
        ],
        //PAKET A
        //PAKET B
        [
            //id =  3
            'hampers_id' => 2,
            'produk_id' => 2,
            'jumlah_produk' => 0.5,
        ],
        [
            //id =  4
            'hampers_id' => 2,
            'produk_id' => 5,
            'jumlah_produk' => 1,
        ],
        //PAKET B
        //PAKET C
        [
            //id =  5
            'hampers_id' => 3,
            'produk_id' => 4,
            'jumlah_produk' => 0.5,
        ],
        [
            //id =  6
            'hampers_id' => 3,
            'produk_id' => 7,
            'jumlah_produk' => 1,
        ],
        //PAKET C
        ]);
    }
}
