<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produks')->insert([[
            'customer_id' => 2,
            'status_cart' => 0,
        ],
        [
            'customer_id' => 2,
            'status_cart' => 1,
        ],
        [
            'customer_id' => 4,
            'status_cart' => 1,
        ],
        [
            'customer_id' => 6,
            'status_cart' => 1,
        ],
        [
            'customer_id' => 8,
            'status_cart' => 1,
        ],
        ]);
    }
}
