<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\City;
use Carbon\Carbon;

class DummyOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data yang diperlukan
        $user = User::first();
        $products = Product::take(3)->get();
        $city = City::first();

        if (!$user || $products->isEmpty() || !$city) {
            $this->command->error('Missing required data: user, products, or city');
            return;
        }

        // Ambil province_id dari city
        $provinceId = $city->province_id ?? 1; // fallback ke 1 jika tidak tersedia

        // Hapus dummy orders lama jika ada
        Order::where('email', 'like', 'test%@example.com')->delete();

        $this->command->info('Creating dummy orders for auto-cancel testing...');

        // Order 1: 25 jam lalu - HARUS DIBATALKAN
        $order1 = $this->createDummyOrder([
            'user_id' => $user->id,
            'email' => 'test1@example.com',
            'status' => 'MENUNGGU PEMBAYARAN',
            'hours_ago' => 25,
            'city_id' => $city->id,
            'province_id' => $provinceId,
        ]);
        $this->createOrderItem($order1->id, $products[0]->id, 2, 25000);
        $this->command->line("✓ Order #{$order1->id} - 25h ago, MENUNGGU PEMBAYARAN (should cancel)");

        // Order 2: 26 jam lalu - HARUS DIBATALKAN
        $order2 = $this->createDummyOrder([
            'user_id' => $user->id,
            'email' => 'test2@example.com',
            'status' => 'PENDING',
            'hours_ago' => 26,
            'city_id' => $city->id,
            'province_id' => $provinceId,
        ]);
        $this->createOrderItem($order2->id, $products[1]->id, 3, 30000);
        $this->command->line("✓ Order #{$order2->id} - 26h ago, PENDING (should cancel)");

        // Order 3: 20 jam lalu - TIDAK DIBATALKAN
        $order3 = $this->createDummyOrder([
            'user_id' => $user->id,
            'email' => 'test3@example.com',
            'status' => 'MENUNGGU PEMBAYARAN',
            'hours_ago' => 20,
            'city_id' => $city->id,
            'province_id' => $provinceId,
        ]);
        $this->createOrderItem($order3->id, $products[2]->id, 1, 50000);
        $this->command->line("✓ Order #{$order3->id} - 20h ago, MENUNGGU PEMBAYARAN (should NOT cancel)");

        // Order 4: 25 jam lalu tapi sudah dibayar - TIDAK DIBATALKAN
        $order4 = $this->createDummyOrder([
            'user_id' => $user->id,
            'email' => 'test4@example.com',
            'status' => 'PEMBAYARAN BERHASIL',
            'hours_ago' => 25,
            'city_id' => $city->id,
            'province_id' => $provinceId,
            'payment_date' => Carbon::now()->subHours(23),
        ]);
        $this->createOrderItem($order4->id, $products[0]->id, 1, 40000);
        $this->command->line("✓ Order #{$order4->id} - 25h ago, PEMBAYARAN BERHASIL (should NOT cancel)");

        // Order 5: 30 jam lalu, sudah dikembalikan stoknya - TIDAK DIBATALKAN LAGI
        $order5 = $this->createDummyOrder([
            'user_id' => $user->id,
            'email' => 'test5@example.com',
            'status' => 'MENUNGGU PEMBAYARAN',
            'hours_ago' => 30,
            'city_id' => $city->id,
            'province_id' => $provinceId,
            'stock_returned' => true,
        ]);
        $this->createOrderItem($order5->id, $products[1]->id, 2, 35000);
        $this->command->line("✓ Order #{$order5->id} - 30h ago, stock already returned (should NOT cancel)");

        $this->command->info("\n=== EXPECTED RESULTS ===");
        $this->command->info("When running 'php artisan orders:auto-cancel --dry-run':");
        $this->command->info("- Should find 2 orders to cancel (Order #{$order1->id} and #{$order2->id})");
        $this->command->info("- Should ignore 3 orders (#{$order3->id}, #{$order4->id}, #{$order5->id})");
    }

    private function createDummyOrder(array $data)
    {
        $baseData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123',
            'postal_code' => '12345',
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total' => 60000,
            'payment_code' => 'ORDER-' . rand(10000, 99999),
            'stock_returned' => false,
        ];

        $merged = array_merge($baseData, $data);

        // Set timestamps
        $createdAt = Carbon::now()->subHours($merged['hours_ago']);
        $merged['created_at'] = $createdAt;
        $merged['updated_at'] = $createdAt;

        unset($merged['hours_ago']);

        return Order::create($merged);
    }

    private function createOrderItem($orderId, $productId, $quantity, $price)
{
    return OrderItem::create([
        'order_id' => $orderId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'price' => $price,
        'total' => $quantity * $price, // ← TAMBAHKAN INI
    ]);
}

}
