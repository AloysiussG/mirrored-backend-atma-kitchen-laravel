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
                'cart_id' => '1',
                'produk_id' => '1',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '850000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '1',
                'produk_id' => '13',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '250000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '3',
                'produk_id' => '4',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '300000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '3',
                'produk_id' => '11',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '100000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '4',
                'produk_id' => '5',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '250000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '4',
                'produk_id' => '10',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '120000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '5',
                'produk_id' => '8',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '200000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '5',
                'produk_id' => '9',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '180000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '6',
                'produk_id' => '12',
                'hampers_id' => NULL,
                'jumlah' => '1',
                'harga_produk_sekarang' => '75000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'cart_id' => '6',
                'produk_id' => NULL,
                'hampers_id' => '1',
                'jumlah' => '1',
                'harga_produk_sekarang' => '650000',
                'created_at' => NULL,
                'updated_at' => NULL
            ],
        ]);
    }
}
