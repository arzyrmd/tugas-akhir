<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ['name' => 'Jawa Tengah', 'code' => 'JTG'],
        ];

        foreach ($provinces as $province) {
            Province::firstOrCreate(
                ['code' => $province['code']], // Cek berdasarkan kode
                $province                       // Kalau belum ada, insert data lengkap
            );
        }
    }
}
