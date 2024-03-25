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
        DB::table('carts')->insert([[
            //id = 1
            'customer_id' => 1,
            'status_cart' => 0,
        ],
        [
            //id = 2
            'customer_id' => 1,
            'status_cart' => 1,
        ],
        [
            //id = 3
            'customer_id' => 2,
            'status_cart' => 0,
        ],
        [
            //id = 4
            'customer_id' => 3,
            'status_cart' => 0,
        ],
        [
            //id = 5
            'customer_id' => 4,
            'status_cart' => 0,
        ],
        [
            //id = 6
            'customer_id' => 5,
            'status_cart' => 0,
        ],
        ]);
    }
}
