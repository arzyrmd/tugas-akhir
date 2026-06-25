<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Province;
use App\Models\City;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset random seed for each session to get different results
        mt_srand();

        // Periksa struktur tabel orders
        $orderColumns = Schema::getColumnListing('orders');
        $hasCanceledDate = in_array('canceled_date', $orderColumns);

        if (!$hasCanceledDate) {
            $this->command->info('Kolom canceled_date tidak ditemukan pada tabel orders. Status DIBATALKAN tidak akan menyimpan tanggal pembatalan.');
        }

        // Gunakan cara yang aman untuk menghapus data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $provinces = Province::pluck('id')->toArray();
        $cities = City::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        $hasUsers = !empty($users);
        $products = Product::where('is_active', true)->get();

        // Periksa apakah data provinsi dan kota tersedia
        if (empty($provinces) || empty($cities)) {
            $this->command->error('Data provinsi atau kota tidak tersedia. Silakan jalankan seeder provinsi dan kota terlebih dahulu.');
            return;
        }

        // Periksa apakah data produk tersedia
        if ($products->isEmpty()) {
            $this->command->error('Data produk tidak tersedia. Silakan jalankan seeder produk terlebih dahulu.');
            return;
        }

        // Tampilkan info jika tidak ada user
        if (!$hasUsers) {
            $this->command->info('Tidak ada user di database. Semua order akan dibuat tanpa user_id.');
        }

        // Data status: Hanya PEMBAYARAN BERHASIL dan DIKEMAS
        // Dengan distribusi: 70% PEMBAYARAN BERHASIL, 30% DIKEMAS
        $statuses = [
            'PEMBAYARAN BERHASIL', 'PEMBAYARAN BERHASIL', 'PEMBAYARAN BERHASIL', 'PEMBAYARAN BERHASIL', 'PEMBAYARAN BERHASIL', 'PEMBAYARAN BERHASIL', 'PEMBAYARAN BERHASIL',
            'DIKEMAS', 'DIKEMAS', 'DIKEMAS'
        ];

        // Bank payment methods
        $paymentMethods = [
            'Bank BCA',
            'Bank Mandiri',
            'Bank BNI',
            'Bank BRI',
            'Bank CIMB Niaga',
            'Bank Permata'
        ];

        // Catatan umum yang mungkin diberikan pelanggan
        $possibleNotes = [
            'Tolong bungkus dengan rapi ya',
            'Kirim secepatnya kalau bisa',
            'Hubungi sebelum kirim',
            'Letakkan paket di depan pintu jika tidak ada orang',
            'Jangan dikirim hari Sabtu/Minggu',
            'Minta tolong dikasih bubble wrap tambahan',
            'Alamat sulit ditemukan, hubungi dulu',
            'Bisa dikirim besok pagi?',
            'Tolong pastikan barang tidak rusak',
            'Pakai kurir yang sama seperti pesanan sebelumnya',
            null, null, null, null, null // Lebih banyak null untuk mengurangi kemungkinan ada catatan
        ];

        // Daftar nama depan Indonesia (laki-laki dan perempuan)
        $indonesianFirstNames = [
            // Laki-laki
            'Budi', 'Agus', 'Dedi', 'Ahmad', 'Bambang', 'Eko', 'Hendra', 'Joko', 'Rudi', 'Slamet',
            'Adi', 'Wawan', 'Dani', 'Dedi', 'Taufik', 'Yudi', 'Iwan', 'Lukman', 'Reza', 'Firman',
            'Andi', 'Anton', 'Arief', 'Bachtiar', 'Kurniawan', 'Dwi', 'Faisal', 'Gunawan', 'Irfan', 'Wahyu',
            'Rizal', 'Satria', 'Indra', 'Hadi', 'Dicky', 'Darmawan', 'Iman', 'Nanang', 'Purnomo', 'Sugeng',
            // Perempuan
            'Dewi', 'Siti', 'Rina', 'Lina', 'Ani', 'Sri', 'Yanti', 'Wati', 'Yuli', 'Erni',
            'Endang', 'Wulan', 'Ratna', 'Lia', 'Nita', 'Fitri', 'Ika', 'Nur', 'Indah', 'Yuni',
            'Dian', 'Lestari', 'Maya', 'Mega', 'Ria', 'Sari', 'Tuti', 'Widya', 'Ayu', 'Retno',
            'Lilis', 'Rini', 'Wiwik', 'Eviyanti', 'Nuraini', 'Farida', 'Herlina', 'Diana', 'Laras', 'Nurul'
        ];

        // Daftar nama belakang Indonesia (beberapa dengan penanda keluarga/marga/etnis)
        $indonesianLastNames = [
            // Umum
            'Wijaya', 'Kusuma', 'Wibowo', 'Santoso', 'Hidayat', 'Saputra', 'Nugroho', 'Kurniawan', 'Sutanto', 'Setiawan',
            'Pratama', 'Utama', 'Permadi', 'Gunawan', 'Hartono', 'Mulyawan', 'Pranata', 'Handoko', 'Budiman', 'Sugiarto',
            'Wicaksono', 'Hermawan', 'Yulianto', 'Ariawan', 'Prasetya', 'Handoyo', 'Purnama', 'Suryanto', 'Susanto', 'Gunardi',
            'Atmaja', 'Irawan', 'Hartanto', 'Waluyo', 'Mulyadi', 'Suryono', 'Kusumo', 'Haryanto', 'Prasetyo', 'Putra',
            // Batak
            'Sitorus', 'Sihotang', 'Simanjuntak', 'Sihombing', 'Siagian', 'Nainggolan', 'Sinaga', 'Tampubolon', 'Hutabarat', 'Siregar',
            // Tionghoa-Indonesia
            'Lim', 'Tan', 'Tjoa', 'Oey', 'Tjan', 'Gunawan', 'Wijaya', 'Halim', 'Salim', 'Santoso',
            // Minang
            'Nasution', 'Harahap', 'Lubis', 'Daulay', 'Tanjung', 'Siregar', 'Hasibuan', 'Barus', 'Batubara', 'Marbun',
            // Sunda
            'Hidayat', 'Kusuma', 'Suryadi', 'Permana', 'Santoso', 'Wijaya', 'Gunawan', 'Maulana', 'Saputra', 'Hermawan',
            // Arab-Indonesia
            'Assegaf', 'Baasyir', 'Shihab', 'Alatas', 'Shahab', 'Syihab', 'Baswedan', 'Alwi', 'Badjerei', 'Bajrei',
            // Tanpa nama belakang
            '', '', '', '', '', '', '', '', '', ''
        ];

        // Buat array untuk memastikan distribusi order sepanjang tahun lebih realistis
        $monthWeights = [
            1 => 70,  // Januari (pasca liburan)
            2 => 60,  // Februari
            3 => 75,  // Maret
            4 => 80,  // April
            5 => 85,  // Mei
            6 => 90,  // Juni (menjelang lebaran)
            7 => 65,  // Juli
            8 => 80,  // Agustus (menjelang kemerdekaan)
            9 => 85,  // September
            10 => 95, // Oktober
            11 => 120, // November (11.11)
            12 => 140, // Desember (liburan akhir tahun)
        ];

        // Buat array bulan berdasarkan bobot
        $weightedMonths = [];
        foreach ($monthWeights as $month => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $weightedMonths[] = $month;
            }
        }

        // Progress bar
        $totalOrders = 120;
        $this->command->info("Membuat $totalOrders orders dengan status PEMBAYARAN BERHASIL dan DIKEMAS...");
        $bar = $this->command->getOutput()->createProgressBar($totalOrders);
        $bar->start();

        $ordersCreated = [];
        $statusCounts = [];

        for ($i = 0; $i < $totalOrders; $i++) {
            // Gunakan waktu sekarang sebagai seed tambahan untuk memastikan keacakan
            $seed = microtime(true) * 1000 + $i;
            mt_srand((int)$seed);

            // Distribusi tahun yang lebih realistis - lebih banyak order di tahun-tahun terbaru
            $yearDistribution = [
                2019 => 5,   // 5%
                2020 => 10,  // 10%
                2021 => 15,  // 15%
                2022 => 15,  // 15%
                2023 => 20,  // 20%
                2024 => 25,  // 25%
                2025 => 10   // 10%
            ];

            $randomValue = mt_rand(1, 100);
            $cumulativeProb = 0;
            $selectedYear = 2025; // default

            foreach ($yearDistribution as $year => $prob) {
                $cumulativeProb += $prob;
                if ($randomValue <= $cumulativeProb) {
                    $selectedYear = $year;
                    break;
                }
            }

            // Pilih bulan berdasarkan bobot
            $month = $weightedMonths[array_rand($weightedMonths)];

            // Atur tanggal dengan memperhatikan jumlah hari dalam bulan
            $daysInMonth = Carbon::createFromDate($selectedYear, $month, 1)->daysInMonth;
            $day = mt_rand(1, $daysInMonth);

            // Buat tanggal dengan Carbon
            $createdAt = Carbon::createFromDate($selectedYear, $month, $day)
                ->setHour(mt_rand(8, 22))
                ->setMinute(mt_rand(0, 59))
                ->setSecond(mt_rand(0, 59));

            // Pastikan tanggal tidak lebih dari sekarang
            if ($createdAt->gt(Carbon::now())) {
                $createdAt = Carbon::now()->subHours(mt_rand(1, 72));
            }

            // Ambil user secara acak atau null
            // Hanya gunakan users jika ada data users di database
            $userId = null;
            $user = null;

            if ($hasUsers && mt_rand(1, 10) <= 8) { // 80% order punya user_id jika ada users
                $userId = $users[array_rand($users)];
                $user = User::find($userId);
            }

            // Buat data order berdasarkan user atau nama Indonesia acak
            if ($user) {
                $firstName = explode(' ', $user->name)[0];
                $lastName = count(explode(' ', $user->name)) > 1 ? substr(strstr($user->name, ' '), 1) : '';
            } else {
                // Gunakan nama Indonesia acak
                $firstName = $indonesianFirstNames[array_rand($indonesianFirstNames)];
                $lastName = $indonesianLastNames[array_rand($indonesianLastNames)];
            }

            // Tambahkan variasi email berdasarkan nama atau acak
            $emailDomains = ['gmail.com', 'yahoo.com', 'yahoo.co.id', 'hotmail.com', 'outlook.com', 'live.com', 'rocketmail.com', 'ymail.com'];

            if ($user) {
                $email = $user->email;
            } else {
                // Buat email dari nama dengan beberapa pola
                $emailPatterns = [
                    strtolower($firstName) . '.' . strtolower(str_replace(' ', '', $lastName ?: mt_rand(10, 99))),
                    strtolower($firstName) . strtolower(str_replace(' ', '', $lastName ?: '')) . mt_rand(1, 999),
                    strtolower($firstName[0]) . strtolower(str_replace(' ', '', $lastName ?: $firstName)) . mt_rand(1, 99),
                    strtolower($firstName) . '.' . mt_rand(1950, 2005),
                    strtolower($firstName) . strtolower($lastName ? $lastName[0] : '') . mt_rand(1, 9999)
                ];

                $emailPattern = $emailPatterns[array_rand($emailPatterns)];
                $emailDomain = $emailDomains[array_rand($emailDomains)];
                $email = $emailPattern . '@' . $emailDomain;
            }

            // Format nomor HP yang realistis untuk Indonesia
            $phoneFormats = [
                '08' . mt_rand(1, 9) . mt_rand(1000000, 9999999),
                '08' . mt_rand(1, 9) . mt_rand(10000000, 99999999),
                '628' . mt_rand(1, 9) . mt_rand(1000000, 9999999),
                '628' . mt_rand(1, 9) . mt_rand(10000000, 99999999),
                '+628' . mt_rand(1, 9) . mt_rand(1000000, 9999999),
                '+628' . mt_rand(1, 9) . mt_rand(10000000, 99999999),
            ];
            $phone = $user ? $user->phone : $phoneFormats[array_rand($phoneFormats)];

            // Alamat acak dengan format Indonesia
            $streetNames = [
                'Jl. Sudirman', 'Jl. Thamrin', 'Jl. Gatot Subroto', 'Jl. Diponegoro', 'Jl. Ahmad Yani',
                'Jl. Pahlawan', 'Jl. Veteran', 'Jl. Merdeka', 'Jl. Asia Afrika', 'Jl. Imam Bonjol',
                'Jl. Sisingamangaraja', 'Jl. Pangeran Antasari', 'Jl. KH. Wahid Hasyim', 'Jl. Hayam Wuruk',
                'Jl. Gajah Mada', 'Jl. Kebon Sirih', 'Jl. Cikini', 'Jl. Salemba', 'Jl. Proklamasi',
                'Jl. Cendana', 'Jl. Tugu', 'Jl. Letjen Suprapto', 'Jl. Juanda', 'Jl. Wahidin',
                'Jl. Cendrawasih', 'Jl. Flamboyan', 'Jl. Melati', 'Jl. Anggrek', 'Jl. Mawar',
                'Jl. Teratai', 'Jl. Dahlia', 'Jl. Kamboja', 'Jl. Kenanga', 'Jl. Bougenville'
            ];

            $houseTypes = [
                'No. ', 'Nomor ', 'No.', '#'
            ];

            $houseSuffixes = [
                '', 'A', 'B', 'C', '/II', '/III', 'RT 01', 'RT 02', 'RT 03', 'RT 04',
                'Blok A', 'Blok B', 'Blok C', 'Blok D', 'Ruko', 'Rukan', 'Kios', 'Rumah'
            ];

            $districts = [
                'Kecamatan Sawah Besar', 'Kecamatan Menteng', 'Kecamatan Tebet', 'Kecamatan Kemayoran', 'Kecamatan Tanah Abang',
                'Kecamatan Kebayoran Baru', 'Kecamatan Cilandak', 'Kecamatan Jagakarsa', 'Kecamatan Pancoran', 'Kecamatan Mampang',
                'Kecamatan Senen', 'Kecamatan Matraman', 'Kecamatan Pulo Gadung', 'Kecamatan Jatinegara', 'Kecamatan Kramat Jati',
                'Kecamatan Makasar', 'Kecamatan Ciracas', 'Kecamatan Cipayung', 'Kecamatan Pasar Rebo', 'Kecamatan Cakung',
                'Kecamatan Tanjung Priok', 'Kecamatan Kelapa Gading', 'Kecamatan Koja', 'Kecamatan Pademangan', 'Kecamatan Penjaringan'
            ];

            $cityNames = [
                'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
                'Tangerang', 'Tangerang Selatan', 'Depok', 'Bekasi', 'Bogor',
                'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Solo',
                'Makassar', 'Medan', 'Palembang', 'Balikpapan', 'Manado',
                'Padang', 'Pontianak', 'Banjarmasin', 'Denpasar', 'Pekanbaru'
            ];

            $address = $user ? $user->address :
                $streetNames[array_rand($streetNames)] . ' ' .
                $houseTypes[array_rand($houseTypes)] . mt_rand(1, 999) .
                ($houseSuffixes[array_rand($houseSuffixes)] ? ' ' . $houseSuffixes[array_rand($houseSuffixes)] : '') . ', ' .
                $districts[array_rand($districts)] . ', ' .
                $cityNames[array_rand($cityNames)];

            // Generate subtotal yang lebih realistis dengan distribusi
            $subtotalRanges = [
                [100000, 250000],  // 30% chance
                [250001, 500000],  // 30% chance
                [500001, 1000000], // 20% chance
                [1000001, 2500000], // 15% chance
                [2500001, 5000000]  // 5% chance
            ];

            $subtotalProbs = [30, 30, 20, 15, 5];
            $randomProb = mt_rand(1, 100);
            $cumulativeProb = 0;
            $selectedRange = $subtotalRanges[0]; // default

            foreach ($subtotalRanges as $index => $range) {
                $cumulativeProb += $subtotalProbs[$index];
                if ($randomProb <= $cumulativeProb) {
                    $selectedRange = $range;
                    break;
                }
            }

            $subtotal = mt_rand($selectedRange[0], $selectedRange[1]);

            // Biaya pengiriman yang lebih realistis berdasarkan subtotal
            $shipping = min(mt_rand(10000, 50000) + floor($subtotal / 1000000) * 20000, 150000);

            // Status: Menggunakan array distribusi yang telah dibuat di atas
            $status = $statuses[array_rand($statuses)];

            // Hitung status untuk statistik
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
            $statusCounts[$status]++;

            // Pilih provinsi dan kota secara acak
            $provinceId = $provinces[array_rand($provinces)];
            $cityId = $cities[array_rand($cities)];

            // Buat kode pembayaran yang lebih realistis
            $paymentPrefixes = ['VA-', 'BCA-', 'MDR-', 'BNI-', 'BRI-', 'PMT-', 'TRF-'];
            $paymentCode = $paymentPrefixes[array_rand($paymentPrefixes)] . strtoupper(Str::random(mt_rand(6, 10)));

            // Pilih catatan secara acak
            $note = $possibleNotes[array_rand($possibleNotes)];

            // Kode pos realistis Indonesia (5 digit)
            $postalCode = mt_rand(10000, 99999);

            // Buat order baru dengan data yang telah digenerate
            $order = Order::create([
                'user_id' => $userId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => $firstName . ' ' . ($lastName ?: ''),
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'province_id' => $provinceId,
                'city_id' => $cityId,
                'postal_code' => $postalCode,
                'notes' => $note,
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'status' => $status,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_code' => $paymentCode,
                'order_created_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(mt_rand(5, 120))
            ]);

            // Atur tanggal proses berdasarkan status order dengan lebih realistis
            // Waktu pembayaran bervariasi dari 10 menit hingga 23 jam setelah order
            $paymentDelay = mt_rand(10, 1380); // 10 menit sampai 23 jam
            $order->payment_date = $createdAt->copy()->addMinutes($paymentDelay);

            if ($status === 'DIKEMAS') {
                $packingDelayHours = mt_rand(3, 24); // 3 jam sampai 24 jam setelah pembayaran
                $order->packing_date = $order->payment_date->copy()->addHours($packingDelayHours);
            }

            $order->save();

            // Buat order items untuk order ini
            $this->createOrderItems($order, $products);

            $ordersCreated[] = $order->id;
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("$totalOrders orders dengan nama-nama Indonesia acak berhasil dibuat!");

        // Tampilkan distribusi status yang dibuat
        $this->command->info("Distribusi status pesanan:");
        foreach ($statusCounts as $status => $count) {
            $percentage = round(($count / $totalOrders) * 100, 1);
            $this->command->info("- $status: $count pesanan ($percentage%)");
        }
    }

    /**
     * Buat order items untuk sebuah order
     */
    private function createOrderItems($order, $products)
    {
        // Jumlah item dalam order (1-5 item)
        $itemCount = mt_rand(1, 5);

        // Pilih produk secara acak tanpa duplikasi
        $selectedProducts = $products->random(min($itemCount, $products->count()))->all();

        $totalItems = 0;
        $orderSubtotal = 0;

        foreach ($selectedProducts as $product) {
            // Jumlah item antara 1-3 untuk setiap produk
            $quantity = mt_rand(1, 3);
            $totalItems += $quantity;

            // Harga produk saat order (bisa sedikit berbeda dari harga sekarang)
            $price = $product->price * (mt_rand(95, 105) / 100); // Variasi harga ±5%
            $price = round($price / 1000) * 1000; // Bulatkan ke 1000

            // Total per item
            $itemTotal = $price * $quantity;
            $orderSubtotal += $itemTotal;

            // Buat order item
            DB::table('order_items')->insert([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $itemTotal,
                'created_at' => $order->created_at,
                'updated_at' => $order->created_at
            ]);
        }

        // Update total order jika perlu
        if ($totalItems > 0) {
            $order->subtotal = $orderSubtotal;
            $order->total = $orderSubtotal + $order->shipping_cost;
            $order->save();
        }
    }
}
