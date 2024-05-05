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
        DB::table('transaksis')->insert([
            [
                //id =  1
                'status_transaksi_id' => 4, //ditolak
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
                'status_transaksi_id' => 4, //ditolak
                'cart_id' => 3,
                'alamat_id' => 8,
                'tanggal_pesan' => '2024-03-15',
                'tanggal_lunas' => '2024-03-16',
                'tanggal_ambil' => '2024-03-22',
                'poin_dipakai' => 0,
                'poin_didapat' => 0,
                'poin_sekarang' => 14,
                'tip' => 10000,
                'ongkos_kirim' => 25000,
                'potongan_harga' => 0,
                'total_harga' => 425000,
                'no_nota' => '24.03.2',
                'kode_bukti_bayar' => '23173808',
                'jenis_pengiriman' => 'delivery',
            ],
            [
                //id =  3
                'status_transaksi_id' => 4, //ditolak
                'cart_id' => 4,
                'alamat_id' => null,
                'tanggal_pesan' => '2024-03-01',
                'tanggal_lunas' => '2024-03-02',
                'tanggal_ambil' => '2024-03-10',
                'poin_dipakai' => 100,
                'poin_didapat' => 0,
                'poin_sekarang' => 28,
                'tip' => 0,
                'ongkos_kirim' => 0,
                'potongan_harga' => 10000,
                'total_harga' => 360000,
                'no_nota' => '24.03.3',
                'kode_bukti_bayar' => '2347390',
                'jenis_pengiriman' => 'pickup',
            ],
            [
                //id =  4
                'status_transaksi_id' => 1, //belum dibayar
                'cart_id' => 5,
                'alamat_id' => 3,
                'tanggal_pesan' => '2024-02-10',
                'tanggal_lunas' => null,
                'tanggal_ambil' => '2024-02-20',
                'poin_dipakai' => 0,
                'poin_didapat' => 0,
                'poin_sekarang' => 100,
                'tip' => 5000,
                'ongkos_kirim' => 10000,
                'potongan_harga' => 0,
                'total_harga' => 390000,
                'no_nota' => '24.02.4',
                'kode_bukti_bayar' => null,
                'jenis_pengiriman' => 'delivery',
            ],
            [
                //id =  5
                'status_transaksi_id' => 10, //selesai
                'cart_id' => 6,
                'alamat_id' => null,
                'tanggal_pesan' => '2024-02-20',
                'tanggal_lunas' => '2024-02-21',
                'tanggal_ambil' => '2024-02-24',
                'poin_dipakai' => 0,
                'poin_didapat' => 107,
                'poin_sekarang' => 87,
                'tip' => 0,
                'ongkos_kirim' => 0,
                'potongan_harga' => 0,
                'total_harga' => 725000,
                'no_nota' => '24.02.5',
                'kode_bukti_bayar' => '2341289',
                'jenis_pengiriman' => 'pickup',
            ],
        ]);
    }
}
