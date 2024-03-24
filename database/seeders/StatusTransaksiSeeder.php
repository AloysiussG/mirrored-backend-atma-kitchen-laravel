<?php

namespace Database\Seeders;

use App\Models\StatusTransaksi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusTransaksiArray = [
            [
                'nama_status' => 'Pesanan belum dibayar',
            ],
            [
                'nama_status' => 'Pesanan diterima',
            ],
            [
                'nama_status' => 'Pesanan diproses',
            ],
            [
                'nama_status' => 'Pesanan siap di-pickup',
            ],
            [
                'nama_status' => 'Pesanan sedang dikirim oleh kurir',
            ],
            // sudah di-pickup == sudah diterima kan?
            [
                'nama_status' => 'Pesanan sudah di-pickup',
            ],
            [
                'nama_status' => 'Pesanan selesai',
            ],
            [
                'nama_status' => 'Pesanan batal',
            ],
        ];

        // FOREACH CREATE MODEL
        foreach ($statusTransaksiArray as $status) {
            StatusTransaksi::create($status);
        }
    }
}
