<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produks')->insert([
            [
                //id =  1
                'status_transaksi_id' => 1, //ditolak
                'cart_id' => 1,
                'alamat_id' => null,
                'tanggal_pesan' => '2024-03-20',
                'tanggal_lunas' => '2024-03-21',
                'tanggal_ambil' => '2024-03-24',
                'poin_dipakai' => 0,
                'poin_didapat' => 0,
                'poin_sekarang' => 10,
                'tip' => 0,
                'ongkos_kirim' => 0,
                'potongan_harga' => 0,
                'total_harga' => 1100000,
                'no_nota' => '24.03.1',
                'kode_bukti_bayar' => '21075379',
                'jenis_pengiriman' => 'pickup',
            ],
            [
                //id =  2
                'status_transaksi_id' => 1, //ditolak
                'cart_id' => 1,
                'alamat_id' => null,
                'tanggal_pesan' => '2024-03-20',
                'tanggal_lunas' => '2024-03-21',
                'tanggal_ambil' => '2024-03-24',
                'poin_dipakai' => 0,
                'poin_didapat' => 0,
                'poin_sekarang' => 10,
                'tip' => 0,
                'ongkos_kirim' => 0,
                'potongan_harga' => 0,
                'total_harga' => 1100000,
                'no_nota' => '24.03.1',
                'kode_bukti_bayar' => '21075379',
                'jenis_pengiriman' => 'pickup',
            ],
        ]);
    }
}
