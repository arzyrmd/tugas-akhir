<?php

namespace App\Observers;

use App\Models\DeliveryBatch;
use App\Models\Order;
use App\Models\CustomProductRequest;

class DeliveryBatchObserver
{
    /**
     * Handle the DeliveryBatch "updated" event.
     */
    public function updated(DeliveryBatch $deliveryBatch): void
    {
        // Jika status berubah, update semua item terkait
        if ($deliveryBatch->isDirty('status')) {

            // Ketika batch mulai dalam perjalanan
            if ($deliveryBatch->status === 'DALAM_PERJALANAN') {

                // Gunakan scheduled_date dari batch
                $batchDate = $deliveryBatch->scheduled_date;

                // 1. Update status DeliveryItem menjadi "DIKIRIM"
                $deliveryBatch->items()->update([
                    'status' => 'DIKIRIM'
                ]);

                // 2. Update status pesanan reguler
                foreach ($deliveryBatch->orders as $order) {
                    $order->status = 'DIKIRIM';
                    $order->delivery_date = $batchDate; // Gunakan tanggal batch, bukan now()
                    $order->save();
                }

                // 3. Update status produk kustom
                foreach ($deliveryBatch->customProducts as $customProduct) {
                    $customProduct->status = 'DIKIRIM';
                    $customProduct->shipping_date = $batchDate; // Gunakan tanggal batch, bukan now()
                    $customProduct->save();
                }
            }

            // Ketika batch selesai
            else if ($deliveryBatch->status === 'SELESAI') {

                // 1. Update status DeliveryItem yang belum diterima menjadi "DITERIMA"
                $deliveryBatch->items()->where('status', '!=', 'DITERIMA')->update([
                    'status' => 'DITERIMA',
                    'delivered_at' => now()
                ]);

                // 2. Update status pesanan reguler yang belum selesai
                foreach ($deliveryBatch->orders()->where('orders.status', '!=', 'SELESAI')->get() as $order) {
                    $order->status = 'SELESAI';
                    $order->completed_date = now();
                    $order->save();
                }

                // 3. Update status produk kustom yang belum selesai
                foreach ($deliveryBatch->customProducts()->where('custom_product_requests.status', '!=', 'SELESAI')->get() as $customProduct) {
                    $customProduct->status = 'SELESAI';
                    $customProduct->delivery_date = now();
                    $customProduct->save();
                }
            }
        }
    }
}
