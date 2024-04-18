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

        $customerArray = array(
            array('nama' => 'Rina Suartini', 'password' => 'daruna53', 'email' => 'daruna53@gmail.com', 'no_telp' => '089154757461', 'saldo' => '13000', 'poin' => '64', 'tanggal_lahir' => '1987-09-20', 'verifyID' => 'testing1', 'verified_at' => '2024-04-17 19:37:32', 'status' => 'Verified'),
            array('nama' => 'Cahya Permadi S.H.', 'password' => 'novitasari.cahyadi', 'email' => 'novitasari.cahyadi@gmail.com', 'no_telp' => '082288631082', 'saldo' => '71000', 'poin' => '242', 'tanggal_lahir' => '1988-07-01', 'verifyID' => 'testing2', 'verified_at' => '2024-04-17 19:37:32', 'status' => 'Verified'),
            array('nama' => 'Najwa Rahimah', 'password' => 'okto54', 'email' => 'okto54@gmail.com', 'no_telp' => '084119884865', 'saldo' => '129000', 'poin' => '258', 'tanggal_lahir' => '1988-12-14', 'verifyID' => 'testing3', 'verified_at' => '2024-04-17 19:37:32', 'status' => 'Verified'),
            array('nama' => 'Garan Taswir Siregar S.Ked', 'password' => 'gpurnawati', 'email' => 'gpurnawati@gmail.com', 'no_telp' => '089468923047', 'saldo' => '144000', 'poin' => '60', 'tanggal_lahir' => '2010-10-09', 'verifyID' => 'testing4', 'verified_at' => '2024-04-17 19:37:32', 'status' => 'Verified'),
            array('nama' => 'Mila Melani', 'password' => 'elvina90', 'email' => 'elvina90@gmail.com', 'no_telp' => '080574781700', 'saldo' => '220000', 'poin' => '228', 'tanggal_lahir' => '1994-12-27', 'verifyID' => 'testing5', 'verified_at' => '2024-04-17 19:37:32', 'status' => 'Verified')
        );

        // FOREACH CREATE MODEL
        foreach ($customerArray as $item) {
            Customer::create($item);
        }


        // Customer::factory()->count(5)->create();

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
