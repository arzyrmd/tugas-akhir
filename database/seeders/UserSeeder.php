<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'address' => 'Jl. Admin Utama No. 1, Jakarta Pusat',
            'phone' => '08123456789',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Daftar kota di Indonesia untuk alamat yang lebih realistis
        $cities = [
            'Jakarta Selatan', 'Jakarta Pusat', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara',
            'Surabaya', 'Bandung', 'Bekasi', 'Medan', 'Tangerang',
            'Depok', 'Semarang', 'Palembang', 'Makassar', 'Bogor',
            'Batam', 'Pekanbaru', 'Bandar Lampung', 'Malang', 'Yogyakarta'
        ];

        // Daftar jalan untuk alamat
        $streets = [
            'Jalan Merdeka', 'Jalan Pahlawan', 'Jalan Ahmad Yani', 'Jalan Sudirman',
            'Jalan Gatot Subroto', 'Jalan Diponegoro', 'Jalan Thamrin', 'Jalan Imam Bonjol',
            'Jalan Veteran', 'Jalan Pemuda', 'Jalan Mawar', 'Jalan Melati',
            'Jalan Cendrawasih', 'Jalan Kenanga', 'Jalan Anggrek', 'Jalan Dahlia',
            'Jalan Teratai', 'Jalan Flamboyan', 'Jalan Cempaka', 'Jalan Seruni'
        ];

        // Awalan nomor telepon Indonesia yang umum
        $phonePrefix = [
            '0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819',
            '0821', '0822', '0823', '0852', '0853', '0851', '0855', '0856',
            '0857', '0858', '0859', '0877', '0878', '0879', '0881', '0882'
        ];

        // Buat 20 user reguler
        for ($i = 1; $i <= 20; $i++) {
            $city = $cities[array_rand($cities)];
            $street = $streets[array_rand($streets)];
            $number = rand(1, 150);
            $prefix = $phonePrefix[array_rand($phonePrefix)];
            $suffix = rand(1000000, 9999999);
            $phone = $prefix . $suffix;

            // Tanggal registrasi dalam 5 tahun terakhir
            $createdAt = Carbon::now()->subYears(rand(0, 5))->subMonths(rand(0, 11))->subDays(rand(0, 30));

            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => $createdAt->copy()->addHours(rand(1, 48)),
                'address' => "{$street} No. {$number}, {$city}",
                'phone' => $phone,
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(rand(5, 60))
            ]);
        }

        $this->command->info('Users have been created successfully!');
    }
}
