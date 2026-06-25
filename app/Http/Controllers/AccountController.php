<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        return view('account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        // Pastikan pengguna hanya melihat pesanannya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('account.order-detail', compact('order'));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        // Pastikan pengguna hanya membatalkan pesanannya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan pesanan masih bisa dibatalkan
        $cancellableStatuses = [
            'MENUNGGU PEMBAYARAN',
            'PEMBAYARAN BERHASIL',
            'DIKEMAS',
            'PENDING',
            'pending'
        ];

        if (!in_array($order->status, $cancellableStatuses)) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena status sudah ' . $order->status);
        }

        try {
            // Kembalikan stok hanya jika belum pernah dikembalikan
            if (!$order->stock_returned) {
                foreach ($order->orderItems as $item) {
                    $product = $item->product;
                    if ($product) {
                        StockMovement::recordStockIn(
                            $product,
                            $item->quantity,
                            'App\Models\Order',
                            $order->id,
                            'Manual: Pengembalian stok karena pembatalan pesanan oleh user'
                        );

                        Log::info("Stock returned for manual cancellation", [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $item->quantity
                        ]);
                    }
                }

                $order->stock_returned = true;
            }

            // Update status pesanan
            $order->status = 'DIBATALKAN';
            $order->cancelled_at = now();
            $order->cancellation_reason = 'Manual: Dibatalkan oleh user';
            $order->save();

            Log::info('Order cancelled manually by user', [
                'order_id' => $order->id,
                'user_id' => $order->user_id
            ]);

            // Pesan untuk user
            if ($order->payment_date) {
                return redirect()->route('account.orders.detail', $order)
                    ->with('success', 'Pesanan berhasil dibatalkan. Stok produk telah dikembalikan. Untuk pengembalian dana, silakan hubungi admin kami.')
                    ->with('show_refund_info', true);
            }

            return redirect()->route('account.orders.detail', $order)
                ->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');

        } catch (\Exception $e) {
            Log::error('Failed to cancel order manually', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal membatalkan pesanan. Silakan coba lagi atau hubungi admin.');
        }
    }
}
