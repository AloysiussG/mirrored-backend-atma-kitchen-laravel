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
            'status' =>'Sedang diproses',
            'nominal' => '1100000',
            'tanggal_refund' => '2024-03-23',
        ],
        [
            'customer_id' => 2,
            'status' =>'Selesai',
            'nominal' => '425000',
            'tanggal_refund' => '2024-03-17',
        ],
        [
            'customer_id' => 3,
            'status' =>'Selesai',
            'nominal' => '360000',
            'tanggal_refund' => '2024-02-03',
        ],
        ]);
    }
}