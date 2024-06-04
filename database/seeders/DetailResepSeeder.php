<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_reseps')->insert([
            [
                'resep_id' => 1,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 500,
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 50,
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 50,
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 300,
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 4,
                'jumlah_bahan_resep' => 100,
            ],
            [
                'resep_id' => 1,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 20,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 500,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 50,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 40,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 300,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 100,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 4,
                'jumlah_bahan_resep' => 100,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 7,
                'jumlah_bahan_resep' => 10,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 8,
                'jumlah_bahan_resep' => 25,
            ],
            [
                'resep_id' => 2,
                'bahan_baku_id' => 9,
                'jumlah_bahan_resep' => 100,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 16,
                'jumlah_bahan_resep' => 250,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 100,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 15,
                'jumlah_bahan_resep' => 50,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 6,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 200,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 150,
            ],
            [
                'resep_id' => 3,
                'bahan_baku_id' => 8,
                'jumlah_bahan_resep' => 60,
            ],
            // spikoe
            [
                'resep_id' => '4',
                'bahan_baku_id' => '3',
                'jumlah_bahan_resep' => '20'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '5',
                'jumlah_bahan_resep' => '200'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '6',
                'jumlah_bahan_resep' => '90'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '23',
                'jumlah_bahan_resep' => '20'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '4',
                'jumlah_bahan_resep' => '10'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '24',
                'jumlah_bahan_resep' => '5'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '1',
                'jumlah_bahan_resep' => '200'
            ],
            [
                'resep_id' => '4',
                'bahan_baku_id' => '10',
                'jumlah_bahan_resep' => '100'
            ],
            // roti sosis
            [
                'resep_id' => '5',
                'bahan_baku_id' => '6',
                'jumlah_bahan_resep' => '250'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '5',
                'jumlah_bahan_resep' => '30'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '25',
                'jumlah_bahan_resep' => '3'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '26',
                'jumlah_bahan_resep' => '3'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '14',
                'jumlah_bahan_resep' => '150'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '1',
                'jumlah_bahan_resep' => '50'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '7',
                'jumlah_bahan_resep' => '2'
            ],
            [
                'resep_id' => '5',
                'bahan_baku_id' => '11',
                'jumlah_bahan_resep' => '10'
            ],
            // milk bun
            [
                'resep_id' => '6',
                'bahan_baku_id' => '6',
                'jumlah_bahan_resep' => '250'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '5',
                'jumlah_bahan_resep' => '30'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '25',
                'jumlah_bahan_resep' => '3'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '26',
                'jumlah_bahan_resep' => '4'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '14',
                'jumlah_bahan_resep' => '300'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '1',
                'jumlah_bahan_resep' => '60'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '7',
                'jumlah_bahan_resep' => '3'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '12',
                'jumlah_bahan_resep' => '200'
            ],
            [
                'resep_id' => '6',
                'bahan_baku_id' => '4',
                'jumlah_bahan_resep' => '50'
            ],
            // matcha creamy latte
            [
                'resep_id' => '7',
                'bahan_baku_id' => '28',
                'jumlah_bahan_resep' => '120'
            ],
            [
                'resep_id' => '7',
                'bahan_baku_id' => '2',
                'jumlah_bahan_resep' => '80'
            ],
            [
                'resep_id' => '7',
                'bahan_baku_id' => '14',
                'jumlah_bahan_resep' => '800'
            ],
            // mandarin
            [
                'resep_id' => 8,
                'bahan_baku_id' => 1,
                'jumlah_bahan_resep' => 300,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 2,
                'jumlah_bahan_resep' => 30,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 3,
                'jumlah_bahan_resep' => 30,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 5,
                'jumlah_bahan_resep' => 200,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 6,
                'jumlah_bahan_resep' => 80,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 4,
                'jumlah_bahan_resep' => 80,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 7,
                'jumlah_bahan_resep' => 5,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 8,
                'jumlah_bahan_resep' => 25,
            ],
            [
                'resep_id' => 8,
                'bahan_baku_id' => 9,
                'jumlah_bahan_resep' => 50,
            ],
            // roti keju
            [
                'resep_id' => '9',
                'bahan_baku_id' => '6',
                'jumlah_bahan_resep' => '250'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '5',
                'jumlah_bahan_resep' => '30'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '25',
                'jumlah_bahan_resep' => '3'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '26',
                'jumlah_bahan_resep' => '3'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '27',
                'jumlah_bahan_resep' => '150'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '1',
                'jumlah_bahan_resep' => '50'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '7',
                'jumlah_bahan_resep' => '2'
            ],
            [
                'resep_id' => '9',
                'bahan_baku_id' => '13',
                'jumlah_bahan_resep' => '350'
            ],
            // choco creamy latte
            [
                'resep_id' => '10',
                'bahan_baku_id' => '8',
                'jumlah_bahan_resep' => '120'
            ],
            [
                'resep_id' => '10',
                'bahan_baku_id' => '2',
                'jumlah_bahan_resep' => '80'
            ],
            [
                'resep_id' => '10',
                'bahan_baku_id' => '14',
                'jumlah_bahan_resep' => '800'
            ],
        ]);
    }
}
