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
        DB::table('detail_carts')->insert([
            [
                //id = 1
                'cart_id' => 1,
                'produk_id' => 1,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 850000,
            ],
            [
                //id = 2
                'cart_id' => 1,
                'produk_id' => 9,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 250000,
            ],
            [
                //id = 3
                'cart_id' => 3,
                'produk_id' => 2,
                'hampers_id' => null,
                'jumlah' => 0.5,
                'harga_produk_sekarang' => 300000,
            ],
            [
                //id = 4
                'cart_id' => 3,
                'produk_id' => 7,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 100000,
            ],
            [
                //id = 5
                'cart_id' => 4,
                'produk_id' => 3,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 250000,
            ],
            [
                //id = 6
                'cart_id' => 4,
                'produk_id' => 6,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 120000,
            ],
            [
                //id = 7
                'cart_id' => 5,
                'produk_id' => 4,
                'hampers_id' => null,
                'jumlah' => 0.5,
                'harga_produk_sekarang' => 200000,
            ],
            [
                //id = 8
                'cart_id' => 5,
                'produk_id' => 5,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 180000,
            ],
            [
                //id = 9
                'cart_id' => 5,
                'produk_id' => 8,
                'hampers_id' => null,
                'jumlah' => 1,
                'harga_produk_sekarang' => 75000,
            ],
            [
                //id = 10
                'cart_id' => 5,
                'produk_id' => null,
                'hampers_id' => 1,
                'jumlah' => 1,
                'harga_produk_sekarang' => 650000,
            ],
        ]);
    }
}
