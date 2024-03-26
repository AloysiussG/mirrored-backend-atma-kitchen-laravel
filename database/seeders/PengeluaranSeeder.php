<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengeluarans')->insert([
            [
                'jenis_pengeluaran' => 'Listrik',
                'total_pengeluaran' => 500000,
                'tanggal_pengeluaran' => '2024-02-09',
            ],
            [
                'jenis_pengeluaran' => 'Iuran RT',
                'total_pengeluaran' => 250000,
                'tanggal_pengeluaran' => '2024-02-10',
            ],
            [
                'jenis_pengeluaran' => 'Bensin',
                'total_pengeluaran' => 20000,
                'tanggal_pengeluaran' => '2024-03-16',
            ],
            [
                'jenis_pengeluaran' => 'Gas',
                'total_pengeluaran' => 210000,
                'tanggal_pengeluaran' => '2024-03-18',
            ],
        ]);
    }
}
