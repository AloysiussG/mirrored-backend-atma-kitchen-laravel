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
        $this->call([
            // UserSeeder::class,
            RoleSeeder::class,
            KaryawanSeeder::class,
            CustomerSeeder::class,
            AlamatSeeder::class,
            StatusTransaksiSeeder::class,
            PresensiSeeder::class,
            PenggajianSeeder::class,

            KategoriProdukSeeder::class,
            PenitipSeeder::class,
            ProdukSeeder::class,
            BahanBakuSeeder::class,
            ResepSeeder::class,
            DetailResepSeeder::class,
            PenggunaanBahanBakuSeeder::class,
            PengadaanBahanBakuSeeder::class,
            PromoPointSeeder::class,
            PengeluaranSeeder::class,

            PermintaanRefundSeeder::class,
            CartSeeder::class,
            HampersSeeder::class,
            DetailHampersSeeder::class,
            DetailCartSeeder::class,
            TransaksiSeeder::class,
        ]);

        // User::factory(10)->create();
    }
}
