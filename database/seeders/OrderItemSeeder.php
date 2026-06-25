<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset random seed for each session to ensure different results
        mt_srand();

        // Truncate existing order items if needed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('order_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil semua orders dan products
        $orders = Order::all();
        $products = Product::where('is_active', true)->get();

        // Periksa apakah data yang diperlukan tersedia
        if ($orders->isEmpty()) {
            $this->command->error('Tidak ada orders di database. Jalankan OrderSeeder terlebih dahulu.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('Tidak ada products aktif di database. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        // Log progress
        $this->command->info('Membuat order items untuk ' . $orders->count() . ' orders...');
        $bar = $this->command->getOutput()->createProgressBar($orders->count());
        $bar->start();

        $totalItems = 0;
        $orderItemsData = [];
        $updateOrdersData = [];

        // Untuk setiap order, buat items dengan jumlah yang realistis
        foreach ($orders as $order) {
            // Tentukan jumlah item berdasarkan nilai subtotal
            $subtotalFactor = $order->subtotal / 100000; // 100rb sebagai basis

            // Gunakan distribusi yang lebih realistis
            // Order kecil (di bawah 500rb) biasanya 1-3 item
            // Order menengah (500rb-2jt) biasanya 2-5 item
            // Order besar (di atas 2jt) biasanya 3-8 item

            if ($order->subtotal < 500000) {
                $minItems = 1;
                $maxItems = min(3, ceil($subtotalFactor / 2));
            } elseif ($order->subtotal < 2000000) {
                $minItems = 2;
                $maxItems = min(5, ceil($subtotalFactor / 3));
            } else {
                $minItems = 3;
                $maxItems = min(8, ceil($subtotalFactor / 4));
            }

            // Pastikan minimal 1 item
            $minItems = max(1, $minItems);
            $maxItems = max($minItems, $maxItems);

            // Tentukan jumlah item
            $orderItemsCount = mt_rand($minItems, $maxItems);

            // Siapkan untuk tracking produk dalam order ini
            $orderSubtotal = 0;
            $usedProductIds = [];

            // Items untuk order ini
            $orderItems = [];

            // Kategori/produk populer memiliki kemungkinan lebih besar untuk muncul di banyak order
            // Di dunia nyata, beberapa produk jauh lebih populer dari yang lain
            $popularProducts = $products->random(min(ceil($products->count() * 0.2), 10))->all();
            $regularProducts = $products->diff($popularProducts)->all();

            // Distribusi: 70% kemungkinan ada produk populer dalam order
            $usePopularProduct = mt_rand(1, 10) <= 7;

            // Jumlah item aktual yang akan dibuat untuk order ini
            $itemsCreated = 0;

            // Fungsi untuk menambahkan item dari koleksi produk tertentu
            $addItemsFromCollection = function($productCollection, $maxToAdd) use (&$itemsCreated, &$orderItems, &$orderSubtotal, &$usedProductIds, $order, &$totalItems) {
                $productsToAdd = min(count($productCollection), $maxToAdd);

                if ($productsToAdd > 0) {
                    // Shuffle array untuk mengacak urutan
                    shuffle($productCollection);

                    for ($i = 0; $i < $productsToAdd; $i++) {
                        if (!isset($productCollection[$i])) continue;

                        $product = $productCollection[$i];

                        // Skip jika produk sudah digunakan dalam order ini
                        if (in_array($product->id, $usedProductIds)) continue;

                        // Tentukan kuantitas yang realistis
                        // Sebagian besar order hanya memiliki 1 atau 2 dari setiap item
                        $quantityDistribution = [1, 1, 1, 1, 2, 2, 3];

                        // Untuk produk mahal (> 1jt), kemungkinan besar hanya beli 1
                        if ($product->price > 1000000) {
                            $quantityDistribution = [1, 1, 1, 1, 1, 2];
                        }
                        // Untuk produk murah (< 100rb), kemungkinan beli lebih banyak
                        elseif ($product->price < 100000) {
                            $quantityDistribution = [1, 1, 2, 2, 3, 3, 4];
                        }

                        $quantity = $quantityDistribution[array_rand($quantityDistribution)];

                        // Variasi harga sedikit (±5%) untuk mensimulasikan perubahan harga seiring waktu
                        $priceVariation = mt_rand(95, 105) / 100;
                        $itemPrice = round($product->price * $priceVariation / 1000) * 1000; // Bulatkan ke 1000 terdekat

                        $itemTotal = $quantity * $itemPrice;

                        // Hanya menyertakan kolom yang ada di tabel, dengan quantity bukan qty
                        $orderItems[] = [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity, // Menggunakan quantity, bukan qty
                            'price' => $itemPrice,
                            'total' => $itemTotal,
                            'created_at' => $order->created_at,
                            'updated_at' => $order->updated_at
                        ];

                        $usedProductIds[] = $product->id;
                        $orderSubtotal += $itemTotal;
                        $itemsCreated++;
                        $totalItems++;
                    }
                }
            };

            // Tambahkan produk populer terlebih dahulu jika flag aktif
            if ($usePopularProduct) {
                $popularToAdd = mt_rand(1, min(3, $orderItemsCount));
                $addItemsFromCollection($popularProducts, $popularToAdd);
            }

            // Tambahkan produk reguler untuk memenuhi jumlah yang diinginkan
            $remainingToAdd = $orderItemsCount - $itemsCreated;
            if ($remainingToAdd > 0) {
                $addItemsFromCollection($regularProducts, $remainingToAdd);
            }

            // Tambahkan ke batch insert data
            $orderItemsData = array_merge($orderItemsData, $orderItems);

            // Update order total
            $updateOrdersData[] = [
                'id' => $order->id,
                'subtotal' => $orderSubtotal,
                'total' => $orderSubtotal + $order->shipping_cost
            ];

            // Batch insert setiap 100 order untuk menghindari query terlalu besar
            if (count($orderItemsData) >= 100) {
                DB::table('order_items')->insert($orderItemsData);
                $orderItemsData = [];
            }

            $bar->advance();
        }

        // Insert sisa data
        if (!empty($orderItemsData)) {
            DB::table('order_items')->insert($orderItemsData);
        }

        // Update orders dengan total yang baru
        foreach ($updateOrdersData as $updateData) {
            Order::where('id', $updateData['id'])->update([
                'subtotal' => $updateData['subtotal'],
                'total' => $updateData['total']
            ]);
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("$totalItems order items berhasil dibuat untuk {$orders->count()} orders!");
    }
}
