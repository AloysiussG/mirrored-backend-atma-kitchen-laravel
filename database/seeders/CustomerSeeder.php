<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()->count(5)->create();

        // $usersArray = User::whereRaw('id % 2 = 0')->get();

        // // FOREACH CREATE MODEL
        // foreach ($usersArray as $user) {
        //     Customer::create([
        //         'user_id' => $user->id,
        //         'nama' => $user->nama,
        //         'password' => $user->password,
        //         'email' => $user->email,
        //         'no_telp' => $user->no_telp,
        //         'saldo' => fake()->numberBetween(0, 250) * 1000,
        //         'poin' => fake()->numberBetween(0, 300),
        //         'tanggal_lahir' => fake()->date(),
        //     ]);
        // }
    }
}
