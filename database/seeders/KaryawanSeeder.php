<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Karyawan::create([
                'role_id' => $i < 3 ? $i + 1 : 4,
                'nama' => fake()->name(),
                'password' => fake()->password(6, 10),
                'email' => fake()->unique()->safeEmail(),
                'no_telp' => '08' . fake()->unique()->numerify('##########'),
                'hire_date' => fake()->date(),
                'gaji' => $i < 3 ? 200000 : 100000,
                'bonus_gaji' => $i < 3 ? 200000 : 100000,
            ]);
        }

        // $usersArray = User::whereRaw('id % 2 = 1')->get();

        // // FOREACH CREATE MODEL
        // foreach ($usersArray as $index => $user) {
        //     Karyawan::create([
        //         'user_id' => $user->id,
        //         'role_id' => $index < 3 ? $index + 1 : 4,
        //         'nama' => $user->nama,
        //         'password' => $user->password,
        //         'email' => $user->email,
        //         'no_telp' => $user->no_telp,
        //         'hire_date' => fake()->date(),
        //         'gaji' => $index < 3 ? 200000 : 100000,
        //         'bonus_gaji' => $index < 3 ? 200000 : 100000,
        //         // 'gaji' => fake()->numberBetween(3, 8) * 1000000,
        //         // 'bonus_gaji' => fake()->numberBetween(1, 5) * 100000,
        //     ]);
        // }
    }
}
