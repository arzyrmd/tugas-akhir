<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransService
{
    /**
     * Initialize Midtrans configuration
     *
     * @param bool $isPayment Whether this is for payment (true) or just status check (false)
     */
    public function initConfig($isPayment = true)
    {
        // Load Midtrans configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        // These are typically only needed for payment operations
        if ($isPayment) {
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;
        }
    }

    /**
     * Generate Snap Token for an order
     *
     * @param Order $order
     * @return string Snap token
     */
    public function generateSnapToken(Order $order, $isRetry = false)
    {
        // Initialize Midtrans configuration
        $this->initConfig();

        // Generate unique transaction order ID
        $transactionOrderId = $this->generateUniqueOrderId($order, $isRetry);

        // Prepare transaction parameters
        $params = [
            'transaction_details' => [
                'order_id' => $transactionOrderId,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => $this->prepareCustomerDetails($order),
            'item_details' => $this->prepareItemDetails($order),
            // Enable only bank transfer (Virtual Account) payment methods
            'enabled_payments' => ['bank_transfer'],
            // Set expiry time to 24 hours (lebih realistis)
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => 60
            ]
        ];

        try {
            // Get Snap Token from Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update order dengan payment code terbaru
            $order->payment_code = $transactionOrderId;
            $order->last_payment_attempt = now();
            $order->save();

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Error generating Snap token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique order ID untuk menghindari duplicate
     *
     * @param Order $order
     * @param bool $isRetry
     * @return string
     */
    private function generateUniqueOrderId(Order $order, $isRetry = false)
    {
        // Base order ID
        $baseOrderId = 'ORDER-' . $order->id;

        // Jika ini retry atau order sudah pernah ada payment_code
        if ($isRetry || !empty($order->payment_code)) {
            // Tambahkan timestamp untuk memastikan unique
            $timestamp = now()->format('YmdHis');
            $random = Str::random(3);
            return $baseOrderId . '-' . $timestamp . '-' . $random;
        }

        // Untuk attempt pertama
        return $baseOrderId . '-' . Str::random(5);
    }
    /**
     * Check status of transaction from Midtrans
     *
     * @param string $orderId
     * @return array Transaction status
     */
    public function getTransactionStatus($orderId)
    {
        $this->initConfig(false);

        try {
            return \Midtrans\Transaction::status($orderId);
        } catch (\Exception $e) {
            Log::error('Error checking transaction status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format payment method name for display
     *
     * @param string $paymentType Payment type from Midtrans
     * @param object|array $notification Notification data from Midtrans
     * @return string Formatted payment method name
     */
    public function formatPaymentMethod($paymentType, $notification)
    {
        // Default payment method name
        $formattedPaymentMethod = 'Pembayaran Online';

        // Only handle bank_transfer since we're limiting to virtual accounts
        if ($paymentType == 'bank_transfer') {
            $bank = null;

            // Check va_numbers in array or object format
            if (is_array($notification) && isset($notification['va_numbers'][0]['bank'])) {
                $bank = $notification['va_numbers'][0]['bank'];
            } elseif (is_object($notification) && isset($notification->va_numbers[0]->bank)) {
                $bank = $notification->va_numbers[0]->bank;
            } elseif (is_array($notification) && isset($notification['permata_va_number'])) {
                $bank = 'permata';
            } elseif (is_object($notification) && isset($notification->permata_va_number)) {
                $bank = 'permata';
            }

            if ($bank) {
                $bankNames = [
                    'bca' => 'Bank BCA',
                    'bni' => 'Bank BNI',
                    'bri' => 'Bank BRI',
                    'mandiri' => 'Bank Mandiri',
                    'permata' => 'Bank Permata',
                ];

                $formattedPaymentMethod = $bankNames[$bank] ?? 'Bank ' . strtoupper($bank);
            } else {
                $formattedPaymentMethod = 'Transfer Bank';
            }
        }

        return $formattedPaymentMethod;
    }

    /**
     * Handle transaction notification from Midtrans
     *
     * @param object $notification Notification data from Midtrans
     * @return Order Updated order
     */
     public function handleNotification($notification)
    {
        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;
        $paymentType = $notification->payment_type;

        // Log notification for debugging
        Log::info('Midtrans notification received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType
        ]);

        // Extract database ID from order_id dengan format yang lebih flexible
        $orderDatabaseId = $this->extractOrderIdFromPaymentCode($orderId);

        if (!$orderDatabaseId) {
            Log::error('Invalid Order ID: ' . $orderId);
            throw new \Exception('Invalid Order ID');
        }

        // Find order in database
        $order = Order::find($orderDatabaseId);

        if (!$order) {
            Log::error('Order not found: ' . $orderDatabaseId);
            throw new \Exception('Order not found');
        }

        // Format payment method
        $formattedPaymentMethod = $this->formatPaymentMethod($paymentType, $notification);

        // Process notification based on status
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'accept' || $fraudStatus == null) {
                // Payment successful
                $order->status = 'PEMBAYARAN BERHASIL';
                $order->payment_date = now();
                $order->payment_method = $formattedPaymentMethod;
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny') {
            // Payment cancelled or denied
            $order->status = 'DIBATALKAN';
        } else if ($transactionStatus == 'expire') {
            // Payment expired
            $order->status = 'GAGAL PEMBAYARAN';
        } else if ($transactionStatus == 'pending') {
            // Payment pending
            $order->status = 'MENUNGGU PEMBAYARAN';
            $order->payment_method = $formattedPaymentMethod . ' (Menunggu)';
        }

        // Save the updated order
        $order->save();

        return $order;
    }

     private function extractOrderIdFromPaymentCode($paymentCode)
    {
        // Format: ORDER-{id}-{random} atau ORDER-{id}-{timestamp}-{random}
        if (preg_match('/^ORDER-(\d+)-/', $paymentCode, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
    /**
     * Prepare customer details for Midtrans
     *
     * @param Order $order
     * @return array Customer details
     */
    private function prepareCustomerDetails(Order $order)
    {
        return [
            'first_name' => $order->first_name,
            'last_name' => $order->last_name,
            'email' => $order->email,
            'phone' => $order->phone,
            'billing_address' => [
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'city' => $order->city->name,
                'postal_code' => $order->postal_code,
                'country_code' => 'IDN'
            ],
            'shipping_address' => [
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'address' => $order->address,
                'city' => $order->city->name,
                'postal_code' => $order->postal_code,
                'country_code' => 'IDN'
            ]
        ];
    }

    /**
     * Prepare item details for Midtrans
     *
     * @param Order $order
     * @return array Item details
     */
    private function prepareItemDetails(Order $order)
    {
        $items = [];

        // Add order items
        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => substr($item->product->name, 0, 50) // Maximum 50 characters
            ];
        }

        // Add shipping cost as an item
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING-' . $order->id,
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman'
            ];
        }

        return $items;
    }
}
