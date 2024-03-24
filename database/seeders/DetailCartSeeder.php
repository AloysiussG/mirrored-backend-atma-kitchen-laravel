<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailCartSeeder extends Seeder
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
        ]);
    }
}
