<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reseps')->insert([
            [
                'produk_id' => 1,
                'nama_resep' => 'Lah-pees Leh-zheet Andalan',
            ],
            [
                'produk_id' => 3,
                'nama_resep' => 'Lapis Surabaya ala Chef Wilson',
            ],
            [
                'produk_id' => 5,
                'nama_resep' => 'Fudgy Brownies by Ramsey',
            ],
        ]);
    }
}
