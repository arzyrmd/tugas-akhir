<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;
class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        // Catat stok keluar saat orderItem dibuat
        try {
            StockMovement::recordStockOut(
                $orderItem->product,
                $orderItem->quantity,
                OrderItem::class,
                $orderItem->id,
                "Penjualan - Order #{$orderItem->order_id}"
            );
        } catch (\Exception $e) {
            // Log error
            Log::error('Gagal mencatat stok keluar: ' . $e->getMessage());
        }
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        // Jika kuantitas berubah, sesuaikan stok
        if ($orderItem->isDirty('quantity')) {
            $oldQuantity = $orderItem->getOriginal('quantity');
            $newQuantity = $orderItem->quantity;
            $difference = $newQuantity - $oldQuantity;

            try {
                if ($difference > 0) {
                    // Jika kuantitas bertambah, kurangi stok
                    StockMovement::recordStockOut(
                        $orderItem->product,
                        $difference,
                        OrderItem::class,
                        $orderItem->id,
                        "Penambahan kuantitas - Order #{$orderItem->order_id}"
                    );
                } elseif ($difference < 0) {
                    // Jika kuantitas berkurang, tambah stok
                    StockMovement::recordStockIn(
                        $orderItem->product,
                        abs($difference),
                        OrderItem::class,
                        $orderItem->id,
                        "Pengurangan kuantitas - Order #{$orderItem->order_id}"
                    );
                }
            } catch (\Exception $e) {
                // Log error
                Log::error('Gagal menyesuaikan stok: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        // Kembalikan stok saat orderItem dihapus
        try {
            StockMovement::recordStockIn(
                $orderItem->product,
                $orderItem->quantity,
                OrderItem::class,
                $orderItem->id,
                "Pembatalan - Order #{$orderItem->order_id}"
            );
        } catch (\Exception $e) {
            // Log error
            Log::error('Gagal mengembalikan stok: ' . $e->getMessage());
        }
    }
}
