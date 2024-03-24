<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bahan_bakus')->insert([
        [
            //1
            'nama_bahan_baku' => 'butter',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 4000
        ],
        [
            //2
            'nama_bahan_baku' => 'creamer',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 4000
        ],
        [
            //3
            'nama_bahan_baku' => 'telur',
            'satuan_bahan' => 'butir',
            'jumlah_bahan_baku' => 100
        ],
        [
            //4
            'nama_bahan_baku' => 'susu bubuk',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 1000
        ],
        [
            //5
            'nama_bahan_baku' => 'gula pasir',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 2000
        ],
        [

            //6
            'nama_bahan_baku' => 'tepung terigu',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 20000
        ],
        [
            //7
            'nama_bahan_baku' => 'garam',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 2000
        ],
        [
            //8
            'nama_bahan_baku' => 'coklat bubuk',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 2000
        ],
        [
            //9
            'nama_bahan_baku' => 'selai strawberry',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 1000
        ],
        [
            //10
            'nama_bahan_baku' => 'kacang kenari',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 1000
        ],
        [
            //11
            'nama_bahan_baku' => 'sosis blackpepper',
            'satuan_bahan' => 'buah',
            'jumlah_bahan_baku' => 100
        ],
        [
            //12
            'nama_bahan_baku' => 'whipped cream',
            'satuan_bahan' => 'mililiter',
            'jumlah_bahan_baku' => 3000
        ],
        [
            //13
            'nama_bahan_baku' => 'keju mozarella',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 3000
        ],
        [
            //14
            'nama_bahan_baku' => 'susu cair',
            'satuan_bahan' => 'mililiter',
            'jumlah_bahan_baku' => 20000
        ],
        [
            //15
            'nama_bahan_baku' => 'minyak goreng',
            'satuan_bahan' => 'mililiter',
            'jumlah_bahan_baku' => 20000
        ],

        [
            //16
            'nama_bahan_baku' => 'coklat batang',
            'satuan_bahan' => 'gram',
            'jumlah_bahan_baku' => 2000
        ],
        ]);
    }
}
