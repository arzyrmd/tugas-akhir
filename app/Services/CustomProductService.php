<?php

namespace App\Services;

use App\Models\CustomProductRequest;
use App\Models\CustomProductReference;
use App\Models\CustomProductProgress;
use App\Models\CustomProductShipment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class CustomProductService
{
    /**
     * Initialize Midtrans configuration
     *
     * @param bool $isPayment Whether this is for payment (true) or just status check (false)
     */
    public function initMidtransConfig($isPayment = true)
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
     * Buat permintaan produk kustom baru
     *
     * @param array $data Data permintaan
     * @param array $referenceImages Gambar referensi yang di-upload
     * @param int|null $userId ID pengguna (opsional)
     * @return CustomProductRequest
     */
    public function createRequest(array $data, array $referenceImages = [], ?int $userId = null)
    {
        // Buat permintaan produk kustom
        $request = CustomProductRequest::create([
            'user_id' => $userId ?? Auth::id(), // Menggunakan Auth::id() yang lebih jelas
            'title' => $data['title'],
            'description' => $data['description'],
            'specifications' => $data['specifications'] ?? null,
            'budget' => $data['budget'] ?? null,
            'desired_deadline' => $data['desired_deadline'] ?? null,
            'status' => 'MENUNGGU_REVIEW',
        ]);

        // Upload dan simpan gambar referensi
        if (!empty($referenceImages)) {
            $this->saveReferenceImages($request, $referenceImages, $data['reference_descriptions'] ?? []);
        }

        return $request;
    }

    /**
     * Upload dan simpan gambar referensi
     *
     * @param CustomProductRequest $request
     * @param array $images
     * @param array $descriptions
     * @return void
     */
    public function saveReferenceImages(CustomProductRequest $request, array $images, array $descriptions = [])
    {
        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('custom-product-references', 'public');

                CustomProductReference::create([
                    'custom_product_request_id' => $request->id,
                    'image_path' => $path,
                    'description' => $descriptions[$index] ?? null,
                ]);
            }
        }
    }

    /**
     * Tambahkan gambar referensi baru ke permintaan
     *
     * @param CustomProductRequest $request
     * @param UploadedFile $image
     * @param string|null $description
     * @return CustomProductReference
     */
    public function addReferenceImage(CustomProductRequest $request, UploadedFile $image, ?string $description = null)
    {
        $path = $image->store('custom-product-references', 'public');

        return CustomProductReference::create([
            'custom_product_request_id' => $request->id,
            'image_path' => $path,
            'description' => $description,
        ]);
    }

    /**
     * Update status permintaan
     *
     * @param CustomProductRequest $request
     * @param string $status
     * @param array $additionalData
     * @return CustomProductRequest
     */
    public function updateStatus(CustomProductRequest $request, string $status, array $additionalData = [])
    {
        $validStatuses = [
            'MENUNGGU_REVIEW',
            'PENAWARAN_DIBERIKAN',
            'PENAWARAN_DITOLAK',
            'MENUNGGU_DP',
            'DALAM_PENGERJAAN',
            'MENUNGGU_PELUNASAN',
            'SIAP_DIKIRIM',
            'DIKIRIM',
            'SELESAI',
            'DIBATALKAN'
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Status tidak valid');
        }

        // Update status
        $request->status = $status;

        // Update tanggal sesuai status
        switch ($status) {
            case 'DALAM_PENGERJAAN':
                $request->work_started_at = now();
                break;
            case 'MENUNGGU_PELUNASAN':
                $request->work_completed_at = now();
                break;
            case 'DIKIRIM':
                $request->shipping_date = now();
                break;
            case 'SELESAI':
                $request->delivery_date = now();
                break;
        }

        // Update data tambahan jika ada
        if (!empty($additionalData)) {
            $request->fill($additionalData);
        }

        $request->save();

        return $request;
    }

    /**
     * Buat penawaran untuk permintaan
     *
     * @param CustomProductRequest $request
     * @param array $offerData
     * @return CustomProductRequest
     */
    public function createOffer(CustomProductRequest $request, array $offerData)
    {
        // Update data penawaran
        $request->quoted_price = $offerData['quoted_price'];
        $request->down_payment = $offerData['down_payment'];
        $request->remaining_payment = $offerData['quoted_price'] - $offerData['down_payment'];
        $request->estimated_completion = $offerData['estimated_completion'];
        $request->admin_notes = $offerData['admin_notes'] ?? null;

        // Update status
        $request->status = 'PENAWARAN_DIBERIKAN';

        $request->save();

        return $request;
    }

    /**
     * Terima penawaran dan mulai proses DP
     *
     * @param CustomProductRequest $request
     * @return CustomProductRequest
     */
    public function acceptOffer(CustomProductRequest $request)
    {
        return $this->updateStatus($request, 'MENUNGGU_DP');
    }

    /**
     * Tolak penawaran
     *
     * @param CustomProductRequest $request
     * @param string|null $reason
     * @return CustomProductRequest
     */
    public function rejectOffer(CustomProductRequest $request, ?string $reason = null)
    {
        return $this->updateStatus($request, 'PENAWARAN_DITOLAK', [
            'admin_notes' => $reason ?? $request->admin_notes
        ]);
    }

    /**
     * Tambahkan progres pengerjaan
     *
     * @param CustomProductRequest $request
     * @param string|UploadedFile $image Path gambar atau objek UploadedFile
     * @param string $description
     * @return CustomProductProgress
     */
    public function addProgressUpdate(CustomProductRequest $request, $image, string $description)
    {
        // Jika $image adalah UploadedFile, simpan dan dapatkan path
        if ($image instanceof UploadedFile) {
            $path = $image->store('custom-product-progresses', 'public');
        } else {
            // Jika $image sudah berupa string path (dari Filament)
            $path = $image;
        }

        return CustomProductProgress::create([
            'custom_product_request_id' => $request->id,
            'image_path' => $path,
            'description' => $description,
        ]);
    }

    /**
     * Buat data pengiriman
     *
     * @param CustomProductRequest $request
     * @param array $shippingData
     * @return CustomProductShipment
     */
    public function createShipment(CustomProductRequest $request, array $shippingData)
    {
        // Log untuk debugging
        Log::info('Creating shipment for custom request', [
            'request_id' => $request->id,
            'shipping_cost' => $shippingData['shipping_cost'] ?? 0
        ]);

        // Cek apakah sudah ada data shipment sebelumnya
        $existingShipment = CustomProductShipment::where('custom_product_request_id', $request->id)->first();

        if ($existingShipment) {
            // Update existing shipment
            Log::info('Updating existing shipment', ['shipment_id' => $existingShipment->id]);

            $existingShipment->full_name = $shippingData['full_name'];
            $existingShipment->email = $shippingData['email'];
            $existingShipment->phone = $shippingData['phone'];
            $existingShipment->address = $shippingData['address'];
            $existingShipment->province_id = $shippingData['province_id'];
            $existingShipment->city_id = $shippingData['city_id'];
            $existingShipment->postal_code = $shippingData['postal_code'];
            $existingShipment->notes = $shippingData['notes'] ?? null;
            $existingShipment->subtotal = $request->quoted_price;
            $existingShipment->shipping_cost = $shippingData['shipping_cost'];
            $existingShipment->total = $request->quoted_price + $shippingData['shipping_cost'];

            $existingShipment->save();

            $shipment = $existingShipment;
        } else {
            // Buat data pengiriman baru
            Log::info('Creating new shipment');

            $shipment = new CustomProductShipment();
            $shipment->custom_product_request_id = $request->id;
            $shipment->full_name = $shippingData['full_name'];
            $shipment->email = $shippingData['email'];
            $shipment->phone = $shippingData['phone'];
            $shipment->address = $shippingData['address'];
            $shipment->province_id = $shippingData['province_id'];
            $shipment->city_id = $shippingData['city_id'];
            $shipment->postal_code = $shippingData['postal_code'];
            $shipment->notes = $shippingData['notes'] ?? null;
            $shipment->subtotal = $request->quoted_price;
            $shipment->shipping_cost = $shippingData['shipping_cost'];
            $shipment->total = $request->quoted_price + $shippingData['shipping_cost'];
            $shipment->status = 'MENUNGGU_PENGIRIMAN';

            $shipment->save();
        }

        // Pastikan status permintaan tetap MENUNGGU_PELUNASAN
        // Jangan update ke SIAP_DIKIRIM karena belum dibayar
        if ($request->status !== 'MENUNGGU_PELUNASAN') {
            $this->updateStatus($request, 'MENUNGGU_PELUNASAN');
        }

        // Update request total dengan shipping cost
        $request->total_price = $request->quoted_price + $shipment->shipping_cost;
        $request->save();

        // Verifikasi bahwa data berhasil disimpan dan terelasi
        $request->refresh();

        Log::info('Shipment created/updated successfully', [
            'request_id' => $request->id,
            'shipment_id' => $shipment->id,
            'shipping_cost' => $shipment->shipping_cost,
            'has_shipment_relation' => $request->shipment ? true : false
        ]);

        return $shipment;
    }

    /**
     * Generate DP payment token
     *
     * @param CustomProductRequest $request
     * @return string Payment token
     */
public function generateDPPaymentToken(CustomProductRequest $request)
{
    // Initialize Midtrans configuration
    $this->initMidtransConfig();

    // SELALU buat kode pembayaran baru untuk menghindari duplikasi Order ID
    $dpPaymentCode = 'CP-DP-' . $request->id . '-' . time() . '-' . Str::random(5);
    $request->dp_payment_code = $dpPaymentCode;
    $request->save();

    // Prepare transaction parameters
    $params = [
        'transaction_details' => [
            'order_id' => $dpPaymentCode,
            'gross_amount' => (int) $request->down_payment,
        ],
        'customer_details' => [
            'first_name' => $request->user->name,
            'email' => $request->user->email,
            'phone' => $request->user->phone ?? '',
        ],
        'item_details' => [
            [
                'id' => 'DP-' . $request->id,
                'price' => (int) $request->down_payment,
                'quantity' => 1,
                'name' => 'Down Payment untuk ' . substr($request->title, 0, 45)
            ]
        ],
        // Virtual Account only
        'enabled_payments' => ['bank_transfer'],
    ];

    try {
        // Get Snap Token from Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return $snapToken;
    } catch (\Exception $e) {
        Log::error('Error generating DP payment token: ' . $e->getMessage(), [
            'request_id' => $request->id,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
    /**
     * Generate full payment token
     *
     * @param CustomProductRequest $request
     * @return string Payment token
     */
   public function generateFullPaymentToken(CustomProductRequest $request)
{
    // Log untuk debugging
    Log::info('Generating full payment token', [
        'request_id' => $request->id,
        'status' => $request->status,
        'has_shipment' => $request->shipment ? true : false
    ]);

    // Pastikan sudah dalam status menunggu pelunasan
    if ($request->status !== 'MENUNGGU_PELUNASAN') {
        Log::error('Request not ready for full payment', [
            'request_id' => $request->id,
            'status' => $request->status
        ]);
        throw new \Exception('Permintaan belum siap untuk pelunasan');
    }

    // Pastikan ada data shipment
    if (!$request->shipment) {
        Log::error('No shipping data found', ['request_id' => $request->id]);
        throw new \Exception('Data pengiriman tidak ditemukan');
    }

    // Initialize Midtrans configuration
    $this->initMidtransConfig();

    // SELALU buat kode pembayaran baru untuk menghindari duplikasi Order ID
    $fullPaymentCode = 'CP-FULL-' . $request->id . '-' . time() . '-' . Str::random(5);
    $request->full_payment_code = $fullPaymentCode;
    $request->save();

    // Ambil data shipment
    $shipping = $request->shipment;
    $shippingCost = $shipping->shipping_cost;

    // Log untuk debugging
    Log::info('Payment details', [
        'remaining_payment' => $request->remaining_payment,
        'shipping_cost' => $shippingCost,
        'total' => $request->remaining_payment + $shippingCost
    ]);

    // Prepare transaction parameters
    $params = [
        'transaction_details' => [
            'order_id' => $fullPaymentCode,
            'gross_amount' => (int) ($request->remaining_payment + $shippingCost),
        ],
        'customer_details' => [
            'first_name' => $shipping->full_name,
            'email' => $shipping->email,
            'phone' => $shipping->phone,
            'shipping_address' => [
                'first_name' => $shipping->full_name,
                'email' => $shipping->email,
                'phone' => $shipping->phone,
                'address' => $shipping->address,
                'postal_code' => $shipping->postal_code
            ]
        ],
        'item_details' => [
            [
                'id' => 'REMAINING-' . $request->id,
                'price' => (int) $request->remaining_payment,
                'quantity' => 1,
                'name' => 'Pelunasan untuk ' . substr($request->title, 0, 45)
            ],
            [
                'id' => 'SHIPPING-' . $request->id,
                'price' => (int) $shippingCost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman'
            ]
        ],
        // Virtual Account only
        'enabled_payments' => ['bank_transfer'],
    ];

    try {
        // Get Snap Token from Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        Log::info('Payment token generated successfully', [
            'request_id' => $request->id,
            'token_length' => strlen($snapToken)
        ]);

        return $snapToken;
    } catch (\Exception $e) {
        Log::error('Error generating full payment token: ' . $e->getMessage(), [
            'request_id' => $request->id,
            'params' => $params,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    /**
     * Check payment status from Midtrans
     *
     * @param string $paymentCode
     * @return array Transaction status
     */
    public function checkPaymentStatus($paymentCode)
    {
        $this->initMidtransConfig(false);

        try {
            return \Midtrans\Transaction::status($paymentCode);
        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle payment notification from Midtrans
     *
     * @param object $notification Notification data from Midtrans
     * @return CustomProductRequest Updated request
     */
   public function handlePaymentNotification($notification)
{
    $orderId = $notification->order_id; // Format: CP-DP-{id}-{timestamp}-{random} or CP-FULL-{id}-{timestamp}-{random}
    $transactionStatus = $notification->transaction_status;
    $fraudStatus = $notification->fraud_status;
    $paymentType = $notification->payment_type;

    // Log notification for debugging
    Log::info('Midtrans custom product notification received', [
        'order_id' => $orderId,
        'transaction_status' => $transactionStatus,
        'payment_type' => $paymentType
    ]);

    // Extract payment type and request ID from order_id
    $orderIdParts = explode('-', $orderId);

    // Cek apakah DP atau Full Payment
    $isDP = ($orderIdParts[1] === 'DP');
    $requestId = $orderIdParts[2] ?? null;

    if (!$requestId) {
        Log::error('Invalid Order ID: ' . $orderId);
        throw new \Exception('Invalid Order ID');
    }

    // Find request in database
    $request = CustomProductRequest::find($requestId);

    if (!$request) {
        Log::error('Custom Product Request not found: ' . $requestId);
        throw new \Exception('Custom Product Request not found');
    }

    // Format payment method (mirip dengan MidtransService)
    $formattedPaymentMethod = $this->formatPaymentMethod($paymentType, $notification);

    // Process notification based on status
    switch ($transactionStatus) {
        case 'capture':
        case 'settlement':
            if ($fraudStatus == 'accept' || $fraudStatus == null) {
                // Payment successful
                if ($isDP) {
                    // DP berhasil dibayar
                    $request->dp_payment_date = now();
                    $request->status = 'DALAM_PENGERJAAN';
                    $request->work_started_at = now();
                    Log::info('DP payment successful for request: ' . $requestId);
                } else {
                    // Pelunasan berhasil
                    $request->full_payment_date = now();
                    $request->status = 'SIAP_DIKIRIM';
                    Log::info('Full payment successful for request: ' . $requestId);
                }
            }
            break;

        case 'pending':
            // Payment masih pending, tidak perlu ubah status
            Log::info('Payment pending for order: ' . $orderId);
            // Status tetap sama, user masih bisa coba bayar lagi
            break;

        case 'cancel':
        case 'deny':
        case 'expire':
            // Payment failed - reset status untuk memungkinkan pembayaran ulang
            if ($isDP) {
                $request->status = 'PENAWARAN_DIBERIKAN'; // Kembali ke status penawaran
                Log::info('DP payment failed/expired for request: ' . $requestId . ', status reset to PENAWARAN_DIBERIKAN');
            } else {
                $request->status = 'MENUNGGU_PELUNASAN'; // Tetap menunggu pelunasan
                Log::info('Full payment failed/expired for request: ' . $requestId . ', status kept as MENUNGGU_PELUNASAN');
            }
            break;

        default:
            Log::warning('Unknown transaction status: ' . $transactionStatus . ' for order: ' . $orderId);
            break;
    }

    // Save the updated request
    $request->save();

    return $request;
}

public function resetPaymentStatus(CustomProductRequest $request, $isDP = true)
{
    if ($isDP) {
        $request->dp_payment_code = null;
        $request->status = 'PENAWARAN_DIBERIKAN';
        Log::info('Reset DP payment status for request: ' . $request->id);
    } else {
        $request->full_payment_code = null;
        $request->status = 'MENUNGGU_PELUNASAN';
        Log::info('Reset full payment status for request: ' . $request->id);
    }

    $request->save();
    return $request;
}

    /**
     * Format payment method name for display
     *
     * @param string $paymentType Payment type from Midtrans
     * @param object|array $notification Notification data from Midtrans
     * @return string Formatted payment method name
     */
    private function formatPaymentMethod($paymentType, $notification)
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
     * Perbarui nomor resi pengiriman
     *
     * @param CustomProductRequest $request
     * @param string $trackingNumber
     * @return CustomProductRequest
     */
    public function updateTrackingNumber(CustomProductRequest $request, string $trackingNumber)
    {
        // Update tracking number pada shipment
        if ($request->shipment) {
            $request->shipment->tracking_number = $trackingNumber;
            $request->shipment->save();
        }

        // Update status menjadi dikirim
        $this->updateStatus($request, 'DIKIRIM', [
            'shipping_date' => now()
        ]);

        return $request;
    }

    /**
     * Selesaikan permintaan
     *
     * @param CustomProductRequest $request
     * @return CustomProductRequest
     */
    public function completeRequest(CustomProductRequest $request)
    {
        return $this->updateStatus($request, 'SELESAI', [
            'delivery_date' => now()
        ]);
    }

    /**
     * Batalkan permintaan
     *
     * @param CustomProductRequest $request
     * @param string|null $reason
     * @return CustomProductRequest
     */
    public function cancelRequest(CustomProductRequest $request, ?string $reason = null)
    {
        return $this->updateStatus($request, 'DIBATALKAN', [
            'admin_notes' => $reason ?? $request->admin_notes
        ]);
    }

    /**
     * Mendapatkan daftar permintaan produk kustom pengguna
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserRequests(int $userId)
    {
        return CustomProductRequest::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mendapatkan detail permintaan produk kustom
     *
     * @param int $requestId
     * @param int|null $userId Opsional, untuk memverifikasi kepemilikan
     * @return CustomProductRequest|null
     */
    public function getRequestDetail(int $requestId, ?int $userId = null)
    {
        $query = CustomProductRequest::with(['references', 'progresses', 'shipment']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->find($requestId);
    }
}
