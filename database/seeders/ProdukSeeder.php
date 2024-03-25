<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produks')->insert([[
            //id =  1
            'kategori_produk_id' => 1,
            'penitip_id' =>null,
            'nama_produk' => 'lapis legit',
            'jumlah_stock' => null,
            'status' => 'Pre Order',
            'harga' => 850000,
            'harga_setengah' => 450000,
            'kuota_harian' => 10,
        ],
        [
            //id =  2
            'kategori_produk_id' => 1,
            'penitip_id' =>null,
            'nama_produk' => 'lapis surabaya',
            'jumlah_stock' => 1,
            'status' => 'Ready Stock',
            'harga' => 550000,
            'harga_setengah' => 300000,
            'kuota_harian' => 10,
        ],
        [
            //id =  3
            'kategori_produk_id' => 1,
            'penitip_id' =>null,
            'nama_produk' => 'brownies',
            'jumlah_stock' => null,
            'status' => 'Pre Order',
            'harga' => 250000,
            'harga_setengah' => 150000,
            'kuota_harian' => 10,
        ],
        [
            //id =  4
            'kategori_produk_id' => 1,
            'penitip_id' =>null,
            'nama_produk' => 'spikoe',
            'jumlah_stock' => null,
            'status' => 'Pre Order',
            'harga' => 350000,
            'harga_setengah' => 200000,
            'kuota_harian' => 10,
        ],
        [
            //id =  5
            'kategori_produk_id' => 2,
            'penitip_id' =>null,
            'nama_produk' => 'roti sosis',
            'jumlah_stock' => null,
            'status' => 'Pre Order',
            'harga' => 180000,
            'harga_setengah' => null,
            'kuota_harian' => 20,
        ],
        [
            //id =  6
            'kategori_produk_id' => 2,
            'penitip_id' =>null,
            'nama_produk' => 'milk bun',
            'jumlah_stock' => 1,
            'status' => 'Ready Stock',
            'harga' => 120000,
            'harga_setengah' => null,
            'kuota_harian' => 20,
        ],
        [
            //id =  7
            'kategori_produk_id' => 3,
            'penitip_id' =>null,
            'nama_produk' => 'matcha creamy latte',
            'jumlah_stock' => 0,
            'status' => 'Pre Order',
            'harga' => 100000,
            'harga_setengah' => null,
            'kuota_harian' => 20,
        ],
        [
            //id =  8
            'kategori_produk_id' => 4,
            'penitip_id' =>1,
            'nama_produk' => 'keripik kentang 250gr',
            'jumlah_stock' => 10,
            'status' => 'Ready Stock',
            'harga' => 75000,
            'harga_setengah' => null,
            'kuota_harian' => 10,
        ],
        [
            //id =  9
            'kategori_produk_id' => 4,
            'penitip_id' =>2,
            'nama_produk' => 'Kopi Luwak Bubuk 250gr',
            'jumlah_stock' => 10,
            'status' => 'Ready Stock',
            'harga' => 250000,
            'harga_setengah' => null,
            'kuota_harian' => 10,
        ],
        ]);
    }
}
