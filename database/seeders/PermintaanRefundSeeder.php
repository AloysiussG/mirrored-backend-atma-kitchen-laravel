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
        DB::table('produks')->insert([[
            'customer_id' => 2,
            'status' =>'Sedang diproses',
            'nominal' => '9999',
            'tanggal_refund' => '2024-03-24',
        ],
        [
            'customer_id' => 4,
            'status' =>'Selesai',
            'nominal' => '9999',
            'tanggal_refund' => '2024-03-22',
        ],
        [
            'customer_id' => 8,
            'status' =>'Selesai',
            'nominal' => '9999',
            'tanggal_refund' => '2024-03-20',
        ],
        ]);
    }
}