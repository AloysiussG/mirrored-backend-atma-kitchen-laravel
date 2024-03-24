<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesArray = [
            [
                'role_name' => 'Owner',
            ],
            [
                'role_name' => 'Admin',
            ],
            [
                'role_name' => 'Manager Operasional',
            ],
            // karyawan atau staff biasa, yang dipresensi oleh MO
            [
                'role_name' => 'Karyawan',
            ],
        ];

        // FOREACH CREATE MODEL
        foreach ($rolesArray as $role) {
            Role::create($role);
        }
    }
}
