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
            'satuan_pembelian' => 'Loyang',
        ],
        [
            'nama_kategori_produk' => 'Roti',
            'satuan_pembelian' => 'Box (isi 10)',
        ],
        [
            'nama_kategori_produk' => 'Minuman',
            'satuan_pembelian' => 'Liter',
        ],
        [
            'nama_kategori_produk' => 'Titipan',
            'satuan_pembelian' => 'Bungkus',
        ]
        ]);
    }
}