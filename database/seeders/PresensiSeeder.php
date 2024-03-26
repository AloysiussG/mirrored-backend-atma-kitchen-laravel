<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Presensi::create(
                [
                    // dimulai dari karyawan id 2 karena Owner tidak termasuk presensi
                    'karyawan_id' => fake()->numberBetween(2, Karyawan::count()),
                    'tanggal_bolos' => fake()->dateTimeBetween('-6 weeks', '2024-03-26'),
                ],
            );
        }

        // $presensiArray = [
        //     [
        //         'karyawan_id' => fake()->numberBetween(1, Karyawan::count()),
        //         'tanggal_bolos' => fake()->dateTimeBetween('-2 weeks', 'now'),
        //     ],
        //     [
        //         'karyawan_id' => fake()->numberBetween(1, Karyawan::count()),
        //         'tanggal_bolos' => fake()->dateTimeBetween('-2 weeks', 'now'),
        //     ],
        // ];

        // // FOREACH CREATE MODEL
        // foreach ($presensiArray as $presensi) {
        //     Presensi::create($presensi);
        // }
    }
}
