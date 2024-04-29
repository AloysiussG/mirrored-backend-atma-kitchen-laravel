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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapislegit1.jpeg',
            ],
            [
                'kategori_produk_id' => 1,
                'penitip_id' => null,
                'nama_produk' => 'Lapis Legit 1/2 Loyang',
                'jumlah_stock' => 1,
                'status' => 'Ready Stock',
                'harga' => 450000,
                'porsi' => 0.5,
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapislegit2.jpeg',
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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapissurabaya1.jpg',
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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/lapissurabaya2.jpg',
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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/brownies1.jpg',
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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/brownies2.jpg',
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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/spikoe1.jpg',
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
                // 'satuan_porsi' => 'Loyang',
                'kuota_harian' => 10,
                'foto_produk' => 'sample/spikoe2.jpg',
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
                // 'satuan_porsi' => null,
                'kuota_harian' => 20,
                'foto_produk' => 'sample/rotisosis.jpg',
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
                // 'satuan_porsi' => null,
                'kuota_harian' => 20,
                'foto_produk' => 'sample/milkbun.jpg',
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
                // 'satuan_porsi' => null,
                'kuota_harian' => 20,
                'foto_produk' => 'sample/matcha.jpg',
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
                // 'satuan_porsi' => null,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/keripikkentang1.jpeg',
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
                // 'satuan_porsi' => null,
                'kuota_harian' => 10,
                'foto_produk' => 'sample/kopiluwak.jpg',
            ],
        ]);
    }
}
