<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoCancelUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'orders:auto-cancel {--dry-run : Run without actually cancelling orders}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically cancel unpaid orders after 24 hours and return stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Debug timing
        $now = Carbon::now();
        $cutoffTime = $now->copy()->subHours(24);

        $this->info('=== AUTO CANCEL DEBUG ===');
        $this->info('Current time: ' . $now->format('Y-m-d H:i:s'));
        $this->info('Cutoff time: ' . $cutoffTime->format('Y-m-d H:i:s'));
        $this->info('Looking for orders created before: ' . $cutoffTime->format('Y-m-d H:i:s'));

        // Cari pesanan yang belum dibayar dan sudah lebih dari 24 jam
        $unpaidOrders = Order::whereIn('status', [
                'MENUNGGU PEMBAYARAN',
                'PENDING',
                'pending'
            ])
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        $this->info('Total unpaid orders: ' . $unpaidOrders->count());

        // Debug setiap order
        foreach ($unpaidOrders as $order) {
            // Gunakan diffInHours tanpa parameter atau dengan true untuk hasil absolut
            $hoursAgo = $order->created_at->diffInHours($now);
            $stockReturned = $order->stock_returned ? 'Yes' : 'No';
            $this->info("Order #{$order->id} - Status: {$order->status} - Created: {$order->created_at} - Hours ago: {$hoursAgo} - Stock returned: {$stockReturned}");
        }

        // Filter yang benar-benar perlu dicancel
        $ordersToCancel = $unpaidOrders->filter(function($order) {
            // Gunakan created_at->diffInHours() untuk hasil yang benar
            $hoursAgo = $order->created_at->diffInHours(now());
            return $hoursAgo >= 24 && !$order->stock_returned;
        });

        if ($ordersToCancel->isEmpty()) {
            $this->info('No orders need to be cancelled (either < 24 hours old or stock already returned).');
            Log::info('Auto-cancel: No orders to cancel');
            return;
        }

        $this->info('Orders to cancel: ' . $ordersToCancel->count());

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No orders will be actually cancelled');
            foreach ($ordersToCancel as $order) {
                // Gunakan created_at->diffInHours() untuk hasil yang benar
                $hoursAgo = $order->created_at->diffInHours(now());
                $this->line("Would cancel Order #{$order->id} - Created: {$order->created_at} ({$hoursAgo} hours ago)");
            }
            return;
        }

        $cancelledCount = 0;
        $errors = 0;

        foreach ($ordersToCancel as $order) {
            try {
                // Return stock untuk setiap item
                foreach ($order->orderItems as $item) {
                    $product = $item->product;
                    if ($product) {
                        StockMovement::recordStockIn(
                            $product,
                            $item->quantity,
                            'App\Models\Order',
                            $order->id,
                            'Auto-cancel: Pengembalian stok karena tidak dibayar dalam 24 jam'
                        );

                        $this->line("Returned {$item->quantity} units of {$product->name}");
                    }
                }

                // Update order status
                $order->status = 'DIBATALKAN';
                $order->stock_returned = true;
                $order->cancelled_at = now();
                $order->cancellation_reason = 'Auto-cancelled: Tidak dibayar dalam 24 jam';
                $order->save();

                $cancelledCount++;
                $this->info("✓ Cancelled Order #{$order->id}");

                Log::info('Auto-cancelled order', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total' => $order->total,
                    'created_at' => $order->created_at
                ]);

            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Failed to cancel Order #{$order->id}: " . $e->getMessage());

                Log::error('Failed to auto-cancel order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("\n=== SUMMARY ===");
        $this->info("Successfully cancelled: {$cancelledCount} orders");
        if ($errors > 0) {
            $this->error("Errors: {$errors} orders");
        }

        Log::info('Auto-cancel completed', [
            'cancelled' => $cancelledCount,
            'errors' => $errors,
            'total_found' => $unpaidOrders->count(),
            'total_processed' => $ordersToCancel->count()
        ]);
    }
}
