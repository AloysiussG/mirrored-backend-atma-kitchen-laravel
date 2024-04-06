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
        $karyawanArray = array(
            array('role_id' => '1', 'nama' => 'Lili Pudjiastuti M.Si', 'password' => 'XJ-8o;cD', 'email' => 'gatot77@example.com', 'no_telp' => '082660300063', 'hire_date' => '1970-08-10', 'gaji' => '200000', 'bonus_gaji' => '200000'),
            array('role_id' => '2', 'nama' => 'Jarwadi Prayoga S.Pd', 'password' => 'VA<8Ri', 'email' => 'zsimanjuntak@example.net', 'no_telp' => '082948865390', 'hire_date' => '2005-05-15', 'gaji' => '200000', 'bonus_gaji' => '200000'),
            array('role_id' => '3', 'nama' => 'Anggabaya Habibi', 'password' => '([Fvmt2rC', 'email' => 'hsalahudin@example.net', 'no_telp' => '080826696228', 'hire_date' => '1994-06-23', 'gaji' => '200000', 'bonus_gaji' => '200000'),
            array('role_id' => '4', 'nama' => 'Rahman Kenzie Pranowo', 'password' => 'uXmf,]cmZ', 'email' => 'setya.nababan@example.net', 'no_telp' => '083547227003', 'hire_date' => '1996-09-17', 'gaji' => '100000', 'bonus_gaji' => '100000'),
            array('role_id' => '4', 'nama' => 'Galiono Raharja Kurniawan', 'password' => 'im2"^>(*', 'email' => 'iswahyudi.ellis@example.com', 'no_telp' => '086847467038', 'hire_date' => '2019-06-01', 'gaji' => '100000', 'bonus_gaji' => '100000')
        );

        // FOREACH CREATE MODEL
        foreach ($karyawanArray as $item) {
            Karyawan::create($item);
        }



        // for ($i = 0; $i < 5; $i++) {
        //     Karyawan::create([
        //         'role_id' => $i < 3 ? $i + 1 : 4,
        //         'nama' => fake()->name(),
        //         'password' => fake()->password(6, 10),
        //         'email' => fake()->unique()->safeEmail(),
        //         'no_telp' => '08' . fake()->unique()->numerify('##########'),
        //         'hire_date' => fake()->date(),
        //         'gaji' => $i < 3 ? 200000 : 100000,
        //         'bonus_gaji' => $i < 3 ? 200000 : 100000,
        //     ]);
        // }

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
