<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama dulu

        // Jawa Tengah
        $jateng = Province::where('code', 'JTG')->first();
        $jatengCities = [
            ['name' => 'Kab. Tegal', 'code' => 'TGL', 'shipping_cost' => 10000], // lokasi toko
            ['name' => 'Kota Tegal', 'code' => 'KTGL', 'shipping_cost' => 10000],
            ['name' => 'Kab. Banyumas', 'code' => 'BYS', 'shipping_cost' => 50000],
            ['name' => 'Kab. Batang', 'code' => 'BTG', 'shipping_cost' => 50000],
            ['name' => 'Kab. Brebes', 'code' => 'BRB', 'shipping_cost' => 20000],
            ['name' => 'Kab. Kebumen', 'code' => 'KBM', 'shipping_cost' => 50000],
            ['name' => 'Kab. Kendal', 'code' => 'KDL', 'shipping_cost' => 40000],
            ['name' => 'Kab. Pekalongan', 'code' => 'PKL', 'shipping_cost' => 35000],
            ['name' => 'Kab. Pemalang', 'code' => 'PML', 'shipping_cost' => 20000],
            ['name' => 'Kab. Purbalingga', 'code' => 'PBG', 'shipping_cost' => 23000],
            ['name' => 'Kota Pekalongan', 'code' => 'KPKL', 'shipping_cost' => 20000],


        ];
        $this->createCities($jateng->id, $jatengCities);
    }

    private function createCities($provinceId, $cities)
    {
        foreach ($cities as $city) {
            City::create([
                'province_id' => $provinceId,
                'name' => $city['name'],
                'code' => $city['code'],
                'shipping_cost' => $city['shipping_cost']
            ]);
        }
    }
}
