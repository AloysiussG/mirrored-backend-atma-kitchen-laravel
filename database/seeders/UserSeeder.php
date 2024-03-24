<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(10)->create();

        // $usersArray = [
        //     [
        //         'nama' => 'A',
        //         'password' => 'a',
        //         'email' => 'a@gmail.com',
        //         'no_telp' => '081938293821',
        //     ],
        //     [
        //         'nama' => 'B',
        //         'password' => 'b',
        //         'email' => 'b@gmail.com',
        //         'no_telp' => '081938293822',
        //     ],
        // ];

        // FOREACH CREATE MODEL
        // foreach ($usersArray as $user) {
        //     User::create($user);
        // }

        // ELOQUENT MODEL
        // User::insert($usersArray);

        // QUERY BUILDER
        // DB::table('users')->insert([]);
    }
}
