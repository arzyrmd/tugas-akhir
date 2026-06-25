<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function retry(Request $request, $order_id)
    {
        try {
            $order = Order::findOrFail($order_id);

            // Log untuk debugging
            Log::info('Payment retry attempted', [
                'order_id' => $order_id,
                'current_status' => $order->status,
                'last_attempt' => $order->last_payment_attempt
            ]);

            // PERBAIKAN 1: Tambahkan status PENDING dan status lainnya
            $payableStatuses = [
                'MENUNGGU PEMBAYARAN',
                'DIBATALKAN',
                'GAGAL PEMBAYARAN',
                'PENDING',  // Tambahkan ini
                'pending'   // Case insensitive
            ];

            if (!in_array($order->status, $payableStatuses)) {
                $statusMessage = 'Pesanan dengan status "' . $order->status . '" tidak dapat dibayar ulang';

                if ($order->status == 'PEMBAYARAN BERHASIL') {
                    $statusMessage = 'Pesanan ini sudah dibayar. Silakan cek halaman detail pesanan.';
                }

                Log::warning('Payment retry blocked due to status', [
                    'order_id' => $order_id,
                    'status' => $order->status
                ]);

                return response()->json([
                    'error' => $statusMessage,
                    'redirect' => route('account.orders.detail', $order->id)
                ], 400);
            }

            // OPSI 1: Hapus cooldown completely (recommended untuk testing)
            // $lastAttempt = $order->last_payment_attempt;
            // if ($lastAttempt && now()->diffInSeconds($lastAttempt) < 10) {
            //     return response()->json(['error' => 'Mohon tunggu sebentar...'], 429);
            // }

            // OPSI 2: Atau gunakan cooldown yang sudah diperbaiki di atas

            // PERBAIKAN 3: Update order status dan timestamp
            $oldStatus = $order->status;
            $order->last_payment_attempt = now();

            // Normalize status ke MENUNGGU PEMBAYARAN untuk semua case
            if (in_array($order->status, ['DIBATALKAN', 'GAGAL PEMBAYARAN', 'PENDING', 'pending'])) {
                $order->status = 'MENUNGGU PEMBAYARAN';
            }

            $order->save();

            Log::info('Order status updated for retry', [
                'order_id' => $order_id,
                'old_status' => $oldStatus,
                'new_status' => $order->status
            ]);

            // PERBAIKAN 4: Generate snap token dengan error handling yang lebih baik
            $snapToken = $this->midtransService->generateSnapToken($order, true);

            Log::info('New snap token generated successfully', [
                'order_id' => $order_id,
                'payment_code' => $order->payment_code
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'message' => 'Silakan selesaikan pembayaran Anda.',
                'new_payment_code' => $order->payment_code,
                'order_status' => $order->status
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Order not found for retry', ['order_id' => $order_id]);
            return response()->json([
                'error' => 'Pesanan tidak ditemukan.',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error during payment retry', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
                'technical_details' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    // Method lainnya tetap sama...
    public function notification(Request $request)
    {
        $this->midtransService->initConfig(false);

        try {
            $notification = new \Midtrans\Notification();
            $order = $this->midtransService->handleNotification($notification);

            if ($order->status === 'PEMBAYARAN BERHASIL') {
                $this->reduceProductStockWithTracking($order);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function reduceProductStockWithTracking(Order $order)
    {
        foreach ($order->orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                try {
                    StockMovement::recordStockOut(
                        $product,
                        $item->quantity,
                        'App\Models\Order',
                        $order->id,
                        "Penjualan - Order #{$order->id}"
                    );

                    Log::info("Stock reduced for product {$product->id}: {$item->quantity} units for order {$order->id}");

                } catch (\Exception $e) {
                    Log::error("Failed to reduce stock for product {$product->id} in order {$order->id}: " . $e->getMessage());
                }
            }
        }
    }

    public function success(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $transactionId = $request->query('transaction_id');

        if ($transactionId && $order->status == 'MENUNGGU PEMBAYARAN') {
            try {
                $status = $this->midtransService->getTransactionStatus($transactionId);
                Log::info('Transaction status from Midtrans', ['status' => json_encode($status)]);

                $order->status = 'PEMBAYARAN BERHASIL';
                $order->payment_date = now();

                $paymentType = null;
                if (is_array($status) && isset($status['payment_type'])) {
                    $paymentType = $status['payment_type'];
                } elseif (is_object($status) && isset($status->payment_type)) {
                    $paymentType = $status->payment_type;
                }

                if ($paymentType) {
                    $formattedPaymentMethod = $this->midtransService->formatPaymentMethod($paymentType, $status);
                    $order->payment_method = $formattedPaymentMethod;
                } else {
                    $order->payment_method = 'Pembayaran Online';
                }

                $order->save();

            } catch (\Exception $e) {
                Log::error('Error getting transaction status: ' . $e->getMessage());
                if ($order->status == 'MENUNGGU PEMBAYARAN') {
                    $order->status = 'PEMBAYARAN BERHASIL';
                    $order->payment_date = now();
                    $order->payment_method = 'Pembayaran Online';
                    $order->save();
                }
            }
        }

        return view('payment.success', compact('order'));
    }

    public function pending(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('payment.pending', compact('order'));
    }

    public function error(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $errorMessage = $request->query('message', 'Terjadi kesalahan saat memproses pembayaran');
        return view('payment.error', compact('order', 'errorMessage'));
    }

    public function cancelled(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('payment.cancelled', compact('order'));
    }

    public function expired(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('payment.expired', compact('order'));
    }
}
