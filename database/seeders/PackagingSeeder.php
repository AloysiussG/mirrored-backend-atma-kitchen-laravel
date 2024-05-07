<?php

namespace Database\Seeders;

use App\Models\Hampers;
use App\Models\Packaging;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackagingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // UNTUK PRODUK CAKE - 1 LOYANG
        // BOX 20x20 CM
        $produk = Produk::query()
            ->where('kategori_produk_id', 1)
            ->where('porsi', 1)
            ->get();

        foreach ($produk as $item) {
            Packaging::create([
                'bahan_baku_id' => 17,
                'produk_id' => $item->id,
                'jumlah' => 1,
            ]);
        }

        // UNTUK PRODUK CAKE - 1/2 LOYANG
        // BOX 20x10 CM
        $produk = Produk::query()
            ->where('kategori_produk_id', 1)
            ->where('porsi', 0.5)
            ->get();

        foreach ($produk as $item) {
            Packaging::create([
                'bahan_baku_id' => 18,
                'produk_id' => $item->id,
                'jumlah' => 1,
            ]);
        }

        // UNTUK PRODUK ROTI
        // BOX 20x10 CM
        $produk = Produk::query()
            ->where('kategori_produk_id', 2)
            ->get();

        foreach ($produk as $item) {
            Packaging::create([
                'bahan_baku_id' => 18,
                'produk_id' => $item->id,
                'jumlah' => 1,
            ]);
        }

        // UNTUK PRODUK MINUMAN
        // BOTOL 1 LITER
        $produk = Produk::query()
            ->where('kategori_produk_id', 3)
            ->get();

        foreach ($produk as $item) {
            Packaging::create([
                'bahan_baku_id' => 19,
                'produk_id' => $item->id,
                'jumlah' => 1,
            ]);
        }

        // UNTUK HAMPERS**
        // - BOX PREMIUM
        // - KARTU UCAPAN
        $hampers = Hampers::all();

        foreach ($hampers as $item) {
            Packaging::create([
                'bahan_baku_id' => 20,
                'hampers_id' => $item->id,
                'jumlah' => 1,
            ]);
            Packaging::create([
                'bahan_baku_id' => 21,
                'hampers_id' => $item->id,
                'jumlah' => 1,
            ]);
        }

        // UNTUK SETIAP 1x TRANSAKSI BERHASIL
        // TAS SPUNBOND 
        $transaksi = Transaksi::query()
            ->where('status_transaksi_id', 6)
            ->Orwhere('status_transaksi_id', 7)
            ->Orwhere('status_transaksi_id', 8)
            ->Orwhere('status_transaksi_id', 9)
            ->Orwhere('status_transaksi_id', 10)
            ->get();

        foreach ($transaksi as $item) {
            Packaging::create([
                'bahan_baku_id' => 22,
                'transaksi_id' => $item->id,
                'jumlah' => 1,
            ]);
        }
    }
}
