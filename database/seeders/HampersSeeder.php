<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HampersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hampers')->insert([
            [
                'nama_hampers' => 'Paket A',
                'harga_hampers' => 650000,
                'foto_hampers' => 'sample/hampersA.jpg',
            ],
            [
                'nama_hampers' => 'Paket B',
                'harga_hampers' => 500000,
                'foto_hampers' => 'sample/hampersB.jpg',
            ],
            [
                'nama_hampers' => 'Paket C',
                'harga_hampers' => 350000,
                'foto_hampers' => 'sample/hampersC.jpg',
            ],
        ]);
    }
}
