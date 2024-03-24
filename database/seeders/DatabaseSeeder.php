<?php

namespace Database\Seeders;


use DB;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       $this->call(
        [
            KategoriProdukSeeder::class,
            penitipSeeder::class,
            ProdukSeeder::class,
            BahanBakuSeeder::class,
            ResepSeeder::class,
            DetailResepSeeder::class,
            PenggunaanBahanBakuSeeder::class,
            PengadaanBahanBakuSeeder::class,
            PromoPointSeeder::class,
            PengeluaranSeeder::class,
        ]
       );
    }
}
