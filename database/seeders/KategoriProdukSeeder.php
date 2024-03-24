<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_produks')->insert([
        [
            'nama_kategori_produk' => 'Cake',
        ],
        [
            'nama_kategori_produk' => 'Roti',
        ],
        [
            'nama_kategori_produk' => 'Minuman',
        ],
        [
            'nama_kategori_produk' => 'Titipan',
        ]
        ]);
    }
}
