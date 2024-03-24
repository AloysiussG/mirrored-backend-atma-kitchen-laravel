<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_produks')->insert([
        [
            'nama' => 'Cake',
        ],
        [
            'nama' => 'Roti',
        ],
        [
            'nama' => 'Minuman',
        ],
        [
            'nama' => 'Titipan',
        ]
        ]);
    }
}
