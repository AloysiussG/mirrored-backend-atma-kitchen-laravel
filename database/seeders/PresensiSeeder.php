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
        $presensiArray = array(
            array('karyawan_id' => '5', 'tanggal_bolos' => '2024-03-25'),
            array('karyawan_id' => '4', 'tanggal_bolos' => '2024-02-21'),
            array('karyawan_id' => '2', 'tanggal_bolos' => '2024-02-27'),
            array('karyawan_id' => '3', 'tanggal_bolos' => '2024-03-07'),
            array('karyawan_id' => '3', 'tanggal_bolos' => '2024-02-06'),
            array('karyawan_id' => '5', 'tanggal_bolos' => '2024-02-18'),
            array('karyawan_id' => '3', 'tanggal_bolos' => '2024-02-29'),
            array('karyawan_id' => '4', 'tanggal_bolos' => '2024-02-11'),
            array('karyawan_id' => '3', 'tanggal_bolos' => '2024-03-02'),
            array('karyawan_id' => '4', 'tanggal_bolos' => '2024-02-11')
        );

        // FOREACH CREATE MODEL
        foreach ($presensiArray as $item) {
            Presensi::create($item);
        }

        // for ($i = 0; $i < 10; $i++) {
        //     Presensi::create(
        //         [
        //             // dimulai dari karyawan id 2 karena Owner tidak termasuk presensi
        //             'karyawan_id' => fake()->numberBetween(2, Karyawan::count()),
        //             'tanggal_bolos' => fake()->dateTimeBetween('-6 weeks', '2024-03-26'),
        //         ],
        //     );
        // }

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
