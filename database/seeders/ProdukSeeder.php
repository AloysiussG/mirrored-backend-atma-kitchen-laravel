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
        DB::table('produks')->truncate();
        DB::table('produk_uniques')->truncate();

        DB::table('produk_uniques')->insert([
            [
                'nama_produk' => 'Lapis Legit',
            ],
            [
                'nama_produk' => 'Lapis Surabaya',
            ],
            [
                'nama_produk' => 'Brownies',
            ],
            [
                'nama_produk' => 'Spikoe',
            ],
            [
                'nama_produk' => 'Roti Sosis',
            ],
            [
                'nama_produk' => 'Milk Bun',
            ],
            [
                'nama_produk' => 'Matcha Creamy Latte',
            ],
            [
                'nama_produk' => 'Keripik Kentang 250gr',
            ],
            [
                'nama_produk' => 'Kopi Luwak Bubuk 250gr',
            ],
        ]);

        DB::table('produks')->insert([
            [
                //id =  1
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Lapis Legit 1 Loyang',
                'jumlah_stock' => null,
                'status' => 'Pre Order',
                'harga' => 850000,
                'porsi' => 1,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapislegit1.jpeg',
                'produk_unique_id' => 1,
            ],
            [
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Lapis Legit 1/2 Loyang',
                'jumlah_stock' => 1,
                'status' => 'Ready Stock',
                'harga' => 450000,
                'porsi' => 0.5,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapislegit2.jpeg',
                'produk_unique_id' => 1,
            ],
            [
                //id =  2
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Lapis Surabaya 1 Loyang',
                'jumlah_stock' => 1,
                'status' => 'Ready Stock',
                'harga' => 550000,
                'porsi' => 1,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapissurabaya1.jpg',
                'produk_unique_id' => 2,
            ],
            [
                //id =  2
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Lapis Surabaya 1/2 Loyang',
                'jumlah_stock' => 1,
                'status' => 'Ready Stock',
                'harga' => 300000,
                'porsi' => 0.5,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapissurabaya2.jpg',
                'produk_unique_id' => 2,
            ],
            [
                //id =  3
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Brownies 1 Loyang',
                'jumlah_stock' => null,
                'status' => 'Pre Order',
                'harga' => 250000,
                'porsi' => 1,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/brownies1.jpg',
                'produk_unique_id' => 3,
            ],
            [
                //id =  3
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Brownies 1/2 Loyang',
                'jumlah_stock' => null,
                'status' => 'Pre Order',
                'harga' => 150000,
                'porsi' => 0.5,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/brownies2.jpg',
                'produk_unique_id' => 3,
            ],
            [
                //id =  4
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Spikoe 1 Loyang',
                'jumlah_stock' => null,
                'status' => 'Pre Order',
                'harga' => 350000,
                'porsi' => 1,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/spikoe1.jpg',
                'produk_unique_id' => 4,
            ],
            [
                //id =  4
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Spikoe 1/2 Loyang',
                'jumlah_stock' => null,
                'status' => 'Pre Order',
                'harga' => 200000,
                'porsi' => 0.5,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/spikoe2.jpg',
                'produk_unique_id' => 4,
            ],
            [
                //id =  5
                'kategori_produk_id' => 2,
                'penitip_id' => null,
                'nama_produk' => 'Roti Sosis',
                'jumlah_stock' => null,
                'status' => 'Pre Order',
                'harga' => 180000,
                'porsi' => null,
                'kuota_harian' => 20,
                'foto_produk' => 'sample/rotisosis.jpg',
                'produk_unique_id' => 5,
            ],
            [
                //id =  6
                'kategori_produk_id' => 2,
                'penitip_id' => null,
                'nama_produk' => 'Milk Bun',
                'jumlah_stock' => 1,
                'status' => 'Ready Stock',
                'harga' => 120000,
                'porsi' => null,
                'kuota_harian' => 20,
                'foto_produk' => 'sample/milkbun.jpg',
                'produk_unique_id' => 6,
            ],
            [
                //id =  7
                'kategori_produk_id' => 3,
                'penitip_id' => null,
                'nama_produk' => 'Matcha Creamy Latte',
                'jumlah_stock' => 0,
                'status' => 'Pre Order',
                'harga' => 100000,
                'porsi' => null,
                'kuota_harian' => 20,
                'foto_produk' => 'sample/matcha.jpg',
                'produk_unique_id' => 7,
            ],
            [
                //id =  8
                'kategori_produk_id' => 4,
                'penitip_id' => 1,
                'nama_produk' => 'Keripik Kentang 250gr',
                'jumlah_stock' => 10,
                'status' => 'Ready Stock',
                'harga' => 75000,
                'porsi' => null,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/keripikkentang1.jpeg',
                'produk_unique_id' => 8,
            ],
            [
                //id =  9
                'kategori_produk_id' => 4,
                'penitip_id' => 2,
                'nama_produk' => 'Kopi Luwak Bubuk 250gr',
                'jumlah_stock' => 10,
                'status' => 'Ready Stock',
                'harga' => 250000,
                'porsi' => null,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/kopiluwak.jpg',
                'produk_unique_id' => 9,
            ],
        ]);
    }
}
