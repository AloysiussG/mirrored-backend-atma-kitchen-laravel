<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermintaanRefundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permintaan_refunds')->insert([[
            'customer_id' => 1,
            'status' =>'berhasil',
            'nominal' => '1100000',
            'tanggal_refund' => '2024-03-23',
            'tanggal_proses' => '2024-03-24 08:00:00'
        ],
        [
            'customer_id' => 2,
            'status' =>'berhasil',
            'nominal' => '425000',
            'tanggal_refund' => '2024-03-17',
            'tanggal_proses' => '2024-03-18 09:00:00'
        ],
        [
            'customer_id' => 3,
            'status' =>'pending',
            'nominal' => '360000',
            'tanggal_refund' => '2024-02-03',
            'tanggal_proses' => null
        ],
        ]);
    }
}
