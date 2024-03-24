<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class PenitipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penitips')->insert([[
            'nama_penitip' => 'Olla',
        ],
        [
            'nama_penitip' => 'Wilson',
        ],
        [
            'nama_penitip' => 'Ella',
        ],
        [
            'nama_penitip' => 'Wim',
        ],
        ]);
    }
}
