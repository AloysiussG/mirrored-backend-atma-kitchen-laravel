<?php

namespace Database\Seeders;

use App\Models\Alamat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlamatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alamatArray = array(
            array('customer_id' => '5', 'alamat_customer' => 'Kpg. Dipenogoro No. 703, Padangpanjang 37951, Kaltim'),
            array('customer_id' => '1', 'alamat_customer' => 'Ki. PHH. Mustofa No. 21, Semarang 17151, Sumut'),
            array('customer_id' => '1', 'alamat_customer' => 'Dk. Bank Dagang Negara No. 490, Palembang 13808, Papua'),
            array('customer_id' => '5', 'alamat_customer' => 'Jln. Untung Suropati No. 506, Palopo 52683, Pabar'),
            array('customer_id' => '5', 'alamat_customer' => 'Jr. Ketandan No. 327, Tidore Kepulauan 79426, Kaltim'),
            array('customer_id' => '4', 'alamat_customer' => 'Jr. Wahidin No. 215, Tebing Tinggi 38484, Sumut'),
            array('customer_id' => '2', 'alamat_customer' => 'Psr. Pacuan Kuda No. 721, Pasuruan 74253, Kepri'),
            array('customer_id' => '4', 'alamat_customer' => 'Ki. Tangkuban Perahu No. 437, Palu 85666, Kalbar'),
            array('customer_id' => '3', 'alamat_customer' => 'Ds. Sam Ratulangi No. 299, Surabaya 16572, Gorontalo'),
            array('customer_id' => '5', 'alamat_customer' => 'Dk. Wahid Hasyim No. 972, Medan 29406, Aceh')
        );

        // FOREACH CREATE MODEL
        foreach ($alamatArray as $item) {
            Alamat::create($item);
        }

        // Alamat::factory()->count(10)->create();
    }
}
