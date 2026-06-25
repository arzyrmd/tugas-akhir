<?php

namespace Database\Seeders;

use App\Models\CustomProductRequest;
use App\Models\CustomProductReference;
use App\Models\CustomProductProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomProductRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa user yang sudah ada atau buat user baru
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }

        // Buat custom product request untuk setiap user
        foreach ($users as $user) {
            $requests = CustomProductRequest::factory(rand(1, 3))
                ->create(['user_id' => $user->id]);

            // Buat referensi gambar untuk setiap request
            foreach ($requests as $request) {
                // Hanya buat referensi gambar jika status bukan MENUNGGU_REVIEW
                if ($request->status !== 'MENUNGGU_REVIEW') {
                    for ($i = 0; $i < rand(1, 3); $i++) {
                        CustomProductReference::create([
                            'custom_product_request_id' => $request->id,
                            'image_path' => 'references/sample-' . rand(1, 5) . '.jpg',
                            'description' => 'Referensi desain ' . ($i + 1),
                        ]);
                    }

                    // Buat progress pengerjaan jika sudah dalam pengerjaan
                    if (in_array($request->status, ['DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'SELESAI'])) {
                        $progressCount = match ($request->status) {
                            'DALAM_PENGERJAAN' => rand(1, 2),
                            'MENUNGGU_PELUNASAN' => rand(3, 4),
                            'SIAP_DIKIRIM', 'SELESAI' => rand(4, 6),
                            default => 0,
                        };

                        for ($i = 0; $i < $progressCount; $i++) {
                            // Menggunakan model CustomProductProgress yang benar untuk mengakses tabel yang benar
                            CustomProductProgress::create([
                                'custom_product_request_id' => $request->id,
                                'image_path' => 'progress/progress-' . rand(1, 10) . '.jpg',
                                'description' => 'Progress pengerjaan hari ke-' . ($i + 1),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
