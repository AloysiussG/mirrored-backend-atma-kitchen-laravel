<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bahan_bakus')->insert([
            // ============== BAHAN BAKU RESEP (BIASA) ==============
            [
                //1
                'nama_bahan_baku' => 'butter',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 4000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //2
                'nama_bahan_baku' => 'creamer',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 4000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //3
                'nama_bahan_baku' => 'telur',
                'satuan_bahan' => 'butir',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //4
                'nama_bahan_baku' => 'susu bubuk',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 1000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //5
                'nama_bahan_baku' => 'gula pasir',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 2000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [

                //6
                'nama_bahan_baku' => 'tepung terigu',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 20000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //7
                'nama_bahan_baku' => 'garam',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 2000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //8
                'nama_bahan_baku' => 'coklat bubuk',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 2000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //9
                'nama_bahan_baku' => 'selai strawberry',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 1000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //10
                'nama_bahan_baku' => 'kacang kenari',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 1000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //11
                'nama_bahan_baku' => 'sosis blackpepper',
                'satuan_bahan' => 'buah',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //12
                'nama_bahan_baku' => 'whipped cream',
                'satuan_bahan' => 'milliliter',
                'jumlah_bahan_baku' => 3000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //13
                'nama_bahan_baku' => 'keju mozzarella',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 3000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //14
                'nama_bahan_baku' => 'susu cair',
                'satuan_bahan' => 'milliliter',
                'jumlah_bahan_baku' => 20000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //15
                'nama_bahan_baku' => 'minyak goreng',
                'satuan_bahan' => 'milliliter',
                'jumlah_bahan_baku' => 20000,
                'jenis_bahan_baku' => 'Resep'
            ],
            [
                //16
                'nama_bahan_baku' => 'coklat batang',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => 2000,
                'jenis_bahan_baku' => 'Resep'
            ],

            // *...jangan tambahin bahan baku disini, soalnya ID Bahan Baku Packaging udah tersetup
            // tambahinnya di bawah packaging...*

            // ============== BAHAN BAKU PACKAGING ==============
            [
                //17
                'nama_bahan_baku' => 'box 20x20 cm',
                'satuan_bahan' => 'box',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Packaging'
            ],
            [
                //18
                'nama_bahan_baku' => 'box 20x10 cm',
                'satuan_bahan' => 'box',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Packaging'
            ],
            [
                //19
                'nama_bahan_baku' => 'botol 1 liter',
                'satuan_bahan' => 'botol',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Packaging'
            ],
            [
                //20
                'nama_bahan_baku' => 'box premium',
                'satuan_bahan' => 'box',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Packaging'
            ],
            [
                //21
                'nama_bahan_baku' => 'kartu ucapan',
                'satuan_bahan' => 'lembar',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Packaging'
            ],
            [
                //22
                'nama_bahan_baku' => 'tas spunbond',
                'satuan_bahan' => 'item',
                'jumlah_bahan_baku' => 100,
                'jenis_bahan_baku' => 'Packaging'
            ],

            // ============== (TAMBAHAN BAHAN BAKU RESEP) ==============
            [
                // 'id' => '23',
                'nama_bahan_baku' => 'tepung maizena',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => '0',
                'jenis_bahan_baku' => 'Resep',
            ],
            [
                // 'id' => '24',
                'nama_bahan_baku' => 'baking powder',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => '0',
                'jenis_bahan_baku' => 'Resep',
            ],
            [
                // 'id' => '25',
                'nama_bahan_baku' => 'ragi',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => '0',
                'jenis_bahan_baku' => 'Resep',
            ],
            [
                // 'id' => '26',
                'nama_bahan_baku' => 'kuning telur',
                'satuan_bahan' => 'buah',
                'jumlah_bahan_baku' => '0',
                'jenis_bahan_baku' => 'Resep',
            ],
            [
                // 'id' => '27',
                'nama_bahan_baku' => 'susu full cream',
                'satuan_bahan' => 'milliliter',
                'jumlah_bahan_baku' => '0',
                'jenis_bahan_baku' => 'Resep',
            ],
            [
                // 'id' => '28',
                'nama_bahan_baku' => 'matcha bubuk',
                'satuan_bahan' => 'gram',
                'jumlah_bahan_baku' => '0',
                'jenis_bahan_baku' => 'Resep',
            ]
        ]);
    }
}
