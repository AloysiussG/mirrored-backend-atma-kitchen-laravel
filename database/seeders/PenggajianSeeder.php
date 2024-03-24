<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Penggajian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenggajianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // karena penggajian karyawan itu tiap bulan (per tgl 2)
        // dan semua karyawan digaji (kecuali owner),
        // aku buat loop per bulan, yang didalemnya ada loop seluruh karyawan

        // array tanggal per bulan (tiap tgl 2)
        $tglPenggajianArray = [
            '2024-03-02',
            '2024-02-02',
            '2024-01-02',
            '2023-12-02',
            '2023-11-02',
        ];

        // array karyawan selain owner
        $karyawanArray = Karyawan::whereRaw('role_id <> 1')->get();

        foreach ($tglPenggajianArray as $tglGaji) {
            foreach ($karyawanArray as $karyawan) {
                Penggajian::create([
                    'karyawan_id' => $karyawan->id,
                    // sementara total gaji aku langsung gaji * 30 + bonus aja
                    // asumsi semua karyawan rajin masuk
                    'total_gaji' => $karyawan->gaji * 30 + $karyawan->bonus_gaji,
                    'tanggal_gaji' => $tglGaji,
                ]);
            }
        }
    }
}
