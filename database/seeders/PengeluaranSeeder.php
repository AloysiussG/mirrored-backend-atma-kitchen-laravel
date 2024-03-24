<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengeluarans')->insert([
        [
            'jenis_pengeluaran' => 'listrik',
            'total_pengeluaran' => 10000,
            'tanggal_pengeluaran' => '2021-10-10',
        ],
        [
            'jenis_pengeluaran' => 'Iuran RT',
            'total_pengeluaran' => 250000,
            'tanggal_pengeluaran' => '2021-10-11',
        ],
        [
            'jenis_pengeluaran' => 'Bensin',
            'total_pengeluaran' => 20000,
            'tanggal_pengeluaran' => '2021-09-11',
        ],
        [
            'jenis_pengeluaran' => 'Gas',
            'total_pengeluaran' => 210000,
            'tanggal_pengeluaran' => '2021-11-11',
        ],
        ]);
    }
}
