<?php

namespace App\Http\Controllers;

use App\Models\CustomProductRequest;
use App\Models\CustomProductReference;
use App\Models\CustomProductProgress;
use App\Models\CustomProductShipment;
use App\Models\Province;
use App\Models\City;
use App\Services\CustomProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductCustomController extends Controller
{
    protected $customProductService;

    public function __construct(CustomProductService $customProductService)
    {
        $this->customProductService = $customProductService;
    }

    /**
     * Tampilkan form request custom product
     */
    public function index()
    {
        return view('custom.index');
    }

    /**
     * Simpan permintaan produk custom baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'desired_deadline' => 'nullable|date|after:today',
            'reference_images.*' => 'nullable|image|max:5120', // Max 5MB per image
            'reference_descriptions.*' => 'nullable|string',
        ]);

        try {
            // Buat custom product request menggunakan service
            $customRequest = $this->customProductService->createRequest(
                $validated,
                $request->file('reference_images') ?? []
            );

            return redirect()->route('custom.show', $customRequest->id)
                ->with('success', 'Permintaan produk custom berhasil dikirim. Admin akan segera meninjau permintaan Anda.');
        } catch (\Exception $e) {
            Log::error('Error creating custom product request: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat permintaan. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan detail permintaan produk custom
     */
    public function show($id)
    {
        // FIX: Cast string ke int
        $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());

        if (!$customRequest) {
            abort(404, 'Permintaan produk custom tidak ditemukan.');
        }

        // Jika ada payment=success dan status masih MENUNGGU_DP, periksa secara manual
        if (request()->has('payment') && request()->get('payment') == 'success' &&
            $customRequest->status == 'MENUNGGU_DP') {
            try {
                // Tambahkan log untuk debug
                Log::info('Checking DP payment status for request #' . $customRequest->id, [
                    'payment_code' => $customRequest->dp_payment_code
                ]);

                // Periksa status pembayaran secara manual
                $paymentStatus = $this->customProductService->checkPaymentStatus($customRequest->dp_payment_code);

                // Log status pembayaran untuk debugging
                Log::info('Payment status check result for request #' . $customRequest->id, [
                    'payment_code' => $customRequest->dp_payment_code,
                    'status_type' => is_array($paymentStatus) ? 'array' : (is_object($paymentStatus) ? 'object' : gettype($paymentStatus)),
                    'transaction_status' => is_array($paymentStatus)
                        ? ($paymentStatus['transaction_status'] ?? 'N/A')
                        : (is_object($paymentStatus) ? ($paymentStatus->transaction_status ?? 'N/A') : 'N/A')
                ]);

                // Jika settlement, update status
                if (is_array($paymentStatus) && isset($paymentStatus['transaction_status']) &&
                    $paymentStatus['transaction_status'] == 'settlement') {
                    $this->customProductService->updateStatus($customRequest, 'DALAM_PENGERJAAN', [
                        'dp_payment_date' => now(),
                        'work_started_at' => now()
                    ]);

                    // Refresh data - FIX: Cast ke int
                    $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());

                    // Log success
                    Log::info('Successfully updated DP payment status for request #' . $customRequest->id);
                } elseif (is_object($paymentStatus) && isset($paymentStatus->transaction_status) &&
                        $paymentStatus->transaction_status == 'settlement') {
                    // Handle jika hasilnya objek
                    $this->customProductService->updateStatus($customRequest, 'DALAM_PENGERJAAN', [
                        'dp_payment_date' => now(),
                        'work_started_at' => now()
                    ]);

                    // Refresh data - FIX: Cast ke int
                    $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());

                    // Log success
                    Log::info('Successfully updated DP payment status for request #' . $customRequest->id);
                }
            } catch (\Exception $e) {
                Log::error('Error checking DP payment for request #' . $customRequest->id . ': ' . $e->getMessage());
            }
        }

        // Cek juga untuk pelunasan
        if (request()->has('payment') && request()->get('payment') == 'success' &&
            $customRequest->status == 'MENUNGGU_PELUNASAN') {
            try {
                // Tambahkan log untuk debug
                Log::info('Checking full payment status for request #' . $customRequest->id, [
                    'payment_code' => $customRequest->full_payment_code
                ]);

                // Periksa status pembayaran secara manual
                $paymentStatus = $this->customProductService->checkPaymentStatus($customRequest->full_payment_code);

                // Log status pembayaran untuk debugging
                Log::info('Full payment status check result for request #' . $customRequest->id, [
                    'payment_code' => $customRequest->full_payment_code,
                    'status_type' => is_array($paymentStatus) ? 'array' : (is_object($paymentStatus) ? 'object' : gettype($paymentStatus)),
                    'transaction_status' => is_array($paymentStatus)
                        ? ($paymentStatus['transaction_status'] ?? 'N/A')
                        : (is_object($paymentStatus) ? ($paymentStatus->transaction_status ?? 'N/A') : 'N/A')
                ]);

                // Jika settlement, update status
                if (is_array($paymentStatus) && isset($paymentStatus['transaction_status']) &&
                    $paymentStatus['transaction_status'] == 'settlement') {
                    $this->customProductService->updateStatus($customRequest, 'SIAP_DIKIRIM', [
                        'full_payment_date' => now()
                    ]);

                    // Refresh data - FIX: Cast ke int
                    $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());

                    // Log success
                    Log::info('Successfully updated full payment status for request #' . $customRequest->id);
                } elseif (is_object($paymentStatus) && isset($paymentStatus->transaction_status) &&
                        $paymentStatus->transaction_status == 'settlement') {
                    // Handle jika hasilnya objek
                    $this->customProductService->updateStatus($customRequest, 'SIAP_DIKIRIM', [
                        'full_payment_date' => now()
                    ]);

                    // Refresh data - FIX: Cast ke int
                    $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());

                    // Log success
                    Log::info('Successfully updated full payment status for request #' . $customRequest->id);
                }
            } catch (\Exception $e) {
                Log::error('Error checking full payment for request #' . $customRequest->id . ': ' . $e->getMessage());
            }
        }

        return view('custom.show', compact('customRequest'));
    }

    /**
     * Tampilkan daftar permintaan produk custom milik user
     */
    public function myRequests()
    {
        // Tampilkan semua custom request milik user dengan paginasi
        $customRequests = CustomProductRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Gunakan paginate(), bukan get() untuk mengaktifkan paginasi

        return view('custom.my-requests', compact('customRequests'));
    }

    /**
     * Terima penawaran dan lanjut ke pembayaran DP
     */
    public function acceptOffer($id)
    {
        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->where('status', 'PENAWARAN_DIBERIKAN')
                ->findOrFail($id);

            // Gunakan service untuk mengupdate status
            $this->customProductService->acceptOffer($customRequest);

            // Redirect ke halaman pembayaran DP
            return redirect()->route('custom.payment.dp', $customRequest->id)
                ->with('success', 'Penawaran berhasil diterima. Silakan lakukan pembayaran DP.');
        } catch (\Exception $e) {
            Log::error('Error accepting offer: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menerima penawaran. Silakan coba lagi.');
        }
    }

    /**
     * Tolak penawaran
     */
    public function rejectOffer(Request $request, $id)
    {
        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->where('status', 'PENAWARAN_DIBERIKAN')
                ->findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            // Gunakan service untuk menolak penawaran
            $this->customProductService->rejectOffer($customRequest, $validated['reason'] ?? null);

            return redirect()->route('custom.my-requests')
                ->with('info', 'Penawaran ditolak. Anda dapat mengajukan permintaan baru kapan saja.');
        } catch (\Exception $e) {
            Log::error('Error rejecting offer: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak penawaran. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan halaman pembayaran DP
     */
        public function paymentDp($id)
    {
        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->whereIn('status', ['MENUNGGU_DP', 'PENAWARAN_DIBERIKAN']) // Tambahkan status alternatif
                ->findOrFail($id);

            // Pastikan status valid untuk pembayaran DP
            if (!in_array($customRequest->status, ['MENUNGGU_DP', 'PENAWARAN_DIBERIKAN'])) {
                return redirect()->route('custom.show', $id)
                    ->with('error', 'Status permintaan tidak valid untuk pembayaran DP.');
            }

            // Generate token pembayaran
            $snapToken = $this->customProductService->generateDPPaymentToken($customRequest);

            return view('custom.payment-dp', compact('customRequest', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Error generating DP payment: ' . $e->getMessage());
            return redirect()->route('custom.show', $id)
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        }
    }

    public function retryPayment($id, $type)
{
    try {
        $customRequest = CustomProductRequest::where('user_id', Auth::id())
            ->findOrFail($id);

        $isDP = ($type === 'dp');

        // Reset payment status
        $this->customProductService->resetPaymentStatus($customRequest, $isDP);

        // Redirect ke halaman pembayaran yang sesuai
        if ($isDP) {
            return redirect()->route('custom.payment.dp', $id)
                ->with('success', 'Status pembayaran telah direset. Silakan coba lagi.');
        } else {
            return redirect()->route('custom.payment.full', $id)
                ->with('success', 'Status pembayaran telah direset. Silakan coba lagi.');
        }

    } catch (\Exception $e) {
        Log::error('Error resetting payment: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan saat mereset pembayaran.');
    }
}

    /**
     * Tampilkan halaman pembayaran pelunasan
     */
    public function paymentFull($id)
    {
        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->where('status', 'MENUNGGU_PELUNASAN')
                ->findOrFail($id);

            // Generate token pembayaran
            $snapToken = $this->customProductService->generateFullPaymentToken($customRequest);

            return view('custom.payment-full', compact('customRequest', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Error generating full payment: ' . $e->getMessage());
            return redirect()->route('custom.show', $id)
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Tambahkan informasi pengiriman
     */
    public function addShipping(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->where('status', 'MENUNGGU_PELUNASAN')
                ->findOrFail($id);

            // Gunakan service untuk menambahkan data pengiriman
            $shipment = $this->customProductService->createShipment($customRequest, $validated);

            return redirect()->route('custom.payment.full', $customRequest->id)
                ->with('success', 'Informasi pengiriman berhasil ditambahkan. Silakan lanjutkan dengan pembayaran pelunasan.');
        } catch (\Exception $e) {
            Log::error('Error adding shipping info: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan informasi pengiriman. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan form informasi pengiriman
     */
    public function shipping($id)
    {
        $customRequest = CustomProductRequest::where('user_id', Auth::id())
            ->where('status', 'MENUNGGU_PELUNASAN')
            ->findOrFail($id);

        // Ambil data provinsi dan kota untuk dropdown
        $provinces = Province::all();

        return view('custom.shipping', compact('customRequest', 'provinces'));
    }

    /**
     * Ambil daftar kota berdasarkan provinsi (untuk AJAX)
     */
    public function getCities(Request $request)
    {
        try {
            $provinceId = $request->province_id;

            // Log for debugging
            Log::info('Looking for cities in province ID: ' . $provinceId);

            // Check if province exists
            $province = Province::find($provinceId);
            if (!$province) {
                return response()->json([
                    'error' => 'Provinsi tidak ditemukan',
                    'cities' => []
                ], 404);
            }

            // Get cities
            $cities = City::where('province_id', $provinceId)
                ->orderBy('name')
                ->get();

            Log::info('Found ' . $cities->count() . ' cities');

            return response()->json([
                'cities' => $cities
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cities: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data kota',
                'message' => $e->getMessage(),
                'cities' => []
            ], 500);
        }
    }

    /**
     * Handle notifikasi pembayaran dari Midtrans
     */
    public function handlePaymentNotification(Request $request)
    {
        try {
            // Config Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            // Tambahkan log untuk debugging
            Log::info('Received payment notification from Midtrans', [
                'raw_input' => $request->getContent()
            ]);

            // Buat notifikasi object
            $notification = new \Midtrans\Notification();

            // Log notifikasi untuk debugging
            Log::info('Payment notification details', [
                'order_id' => $notification->order_id ?? 'N/A',
                'transaction_status' => $notification->transaction_status ?? 'N/A',
                'payment_type' => $notification->payment_type ?? 'N/A'
            ]);

            // Handle notification using service
            $customRequest = $this->customProductService->handlePaymentNotification($notification);

            // Log sukses
            Log::info('Successfully processed payment notification for order: ' . ($notification->order_id ?? 'N/A'));

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error handling payment notification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tandai permintaan sebagai selesai oleh pelanggan
     */
    public function markComplete($id)
    {
        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->where('status', 'DIKIRIM')
                ->findOrFail($id);

            // Gunakan service untuk menandai selesai
            $this->customProductService->completeRequest($customRequest);

            return redirect()->route('custom.show', $id)
                ->with('success', 'Terima kasih! Permintaan produk custom Anda telah ditandai selesai.');
        } catch (\Exception $e) {
            Log::error('Error marking request as complete: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan permintaan. Silakan coba lagi.');
        }
    }

    /**
     * Tambahkan gambar referensi tambahan
     */
    public function addReference(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->whereIn('status', ['MENUNGGU_REVIEW', 'PENAWARAN_DIBERIKAN'])
                ->findOrFail($id);

            // Gunakan service untuk menambahkan gambar referensi
            $reference = $this->customProductService->addReferenceImage(
                $customRequest,
                $request->file('image'),
                $validated['description'] ?? null
            );

            return redirect()->route('custom.show', $id)
                ->with('success', 'Gambar referensi berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error adding reference image: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan gambar referensi. Silakan coba lagi.');
        }
    }

    public function paymentDpSuccess($id)
{
    try {
        $customRequest = CustomProductRequest::where('user_id', Auth::id())
            ->whereIn('status', ['DALAM_PENGERJAAN', 'MENUNGGU_DP']) // Status setelah DP berhasil atau masih pending
            ->findOrFail($id);

        // Pastikan ada data pembayaran DP
        if (!$customRequest->dp_payment_code) {
            return redirect()->route('custom.show', $id)
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Cek status pembayaran terkini
        try {
            $paymentStatus = $this->customProductService->checkPaymentStatus($customRequest->dp_payment_code);

            // Log untuk debugging
            Log::info('Payment status check for success page', [
                'request_id' => $customRequest->id,
                'payment_code' => $customRequest->dp_payment_code,
                'status' => $paymentStatus
            ]);

            // Update status jika pembayaran settlement tapi status belum terupdate
            if ((is_array($paymentStatus) && isset($paymentStatus['transaction_status']) && $paymentStatus['transaction_status'] == 'settlement') ||
                (is_object($paymentStatus) && isset($paymentStatus->transaction_status) && $paymentStatus->transaction_status == 'settlement')) {

                if ($customRequest->status == 'MENUNGGU_DP') {
                    $this->customProductService->updateStatus($customRequest, 'DALAM_PENGERJAAN', [
                        'dp_payment_date' => now(),
                        'work_started_at' => now()
                    ]);

                    // Refresh data - FIX: Cast ke int
                    $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error checking payment status on success page: ' . $e->getMessage());
        }

        return view('custom.payment-dp-success', compact('customRequest'));

    } catch (\Exception $e) {
        Log::error('Error displaying DP payment success page: ' . $e->getMessage());
        return redirect()->route('custom.show', $id)
            ->with('error', 'Terjadi kesalahan saat menampilkan halaman pembayaran.');
    }
}

/**
 * Tampilkan halaman bukti pembayaran pelunasan berhasil
 */
    public function paymentFullSuccess($id)
    {
        try {
            $customRequest = CustomProductRequest::where('user_id', Auth::id())
                ->whereIn('status', ['SIAP_DIKIRIM', 'MENUNGGU_PELUNASAN']) // Status setelah pelunasan berhasil atau masih pending
                ->findOrFail($id);

            // Pastikan ada data pembayaran pelunasan
            if (!$customRequest->full_payment_code) {
                return redirect()->route('custom.show', $id)
                    ->with('error', 'Data pembayaran tidak ditemukan.');
            }

            // Cek status pembayaran terkini
            try {
                $paymentStatus = $this->customProductService->checkPaymentStatus($customRequest->full_payment_code);

                // Update status jika pembayaran settlement tapi status belum terupdate
                if ((is_array($paymentStatus) && isset($paymentStatus['transaction_status']) && $paymentStatus['transaction_status'] == 'settlement') ||
                    (is_object($paymentStatus) && isset($paymentStatus->transaction_status) && $paymentStatus->transaction_status == 'settlement')) {

                    if ($customRequest->status == 'MENUNGGU_PELUNASAN') {
                        $this->customProductService->updateStatus($customRequest, 'SIAP_DIKIRIM', [
                            'full_payment_date' => now()
                        ]);

                        // Refresh data - FIX: Cast ke int
                        $customRequest = $this->customProductService->getRequestDetail((int) $id, Auth::id());
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error checking payment status on success page: ' . $e->getMessage());
            }

            return view('custom.payment-full-success', compact('customRequest'));

        } catch (\Exception $e) {
            Log::error('Error displaying full payment success page: ' . $e->getMessage());
            return redirect()->route('custom.show', $id)
                ->with('error', 'Terjadi kesalahan saat menampilkan halaman pembayaran.');
        }
    }
}
