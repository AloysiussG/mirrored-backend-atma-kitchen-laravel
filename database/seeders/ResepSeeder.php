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
                'nama_resep' => 'Lapis Legit andalan', 
            ],
            [
                'produk_id' => 2,
                'nama_resep' => 'Lapis Surabaya Chef Wilson', 
            ],
            [
                'produk_id' => 3,
                'nama_resep' => 'Fudgy Brownies by Ramsey', 
            ],
            ]);
    }
}
