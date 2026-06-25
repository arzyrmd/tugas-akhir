@extends('layouts.app')

@section('styles')
    <style>
        /* Payment Full Page - Amado Style */
        .payment-full-page {
            margin-bottom: 50px;
        }

        .payment-header {
            border-bottom: 3px solid #fbb710;
            padding-bottom: 25px;
            margin-bottom: 40px;
            background-color: #f5f7fa;
            padding: 30px;
        }

        .payment-header h2 {
            color: #242424;
            font-size: 30px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .payment-header p {
            color: #6d6d6d;
            font-size: 16px;
            margin-bottom: 0;
        }

        /* Status Badge - Amado Style */
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 0;
            background-color: #fbb710;
            color: #fff;
            border: 1px solid #fbb710;
        }

        /* Custom Request Details - Amado Style */
        .product-details-section {
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .product-details-section h3 {
            color: #242424;
            font-size: 20px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            position: relative;
        }

        .product-details-section h3::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #fbb710;
        }

        /* Product Table - Amado Style */
        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e0e0e0;
            border-radius: 0;
        }

        .product-table thead th {
            background-color: #f5f7fa;
            color: #242424;
            font-weight: 600;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .product-table tbody td {
            padding: 20px 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #6d6d6d;
            font-size: 14px;
            vertical-align: middle;
        }

        .product-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Product Image - Square Ratio */
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0;
            border: 2px solid #28a745;
            margin-right: 15px;
        }

        .product-image-placeholder {
            width: 60px;
            height: 60px;
            background-color: #f8f9fa;
            border: 2px dashed #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            border-radius: 0;
        }

        .product-info h5 {
            color: #242424;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .product-status {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 0;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Payment Summary - Amado Style */
        .payment-summary-section {
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .payment-summary-section h4 {
            color: #242424;
            font-size: 20px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            position: relative;
        }

        .payment-summary-section h4::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: #fbb710;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f7fa;
        }

        .summary-item:last-child {
            border-bottom: 2px solid #fbb710;
            padding-top: 15px;
            margin-top: 10px;
        }

        .summary-item span:first-child {
            color: #6d6d6d;
            font-size: 14px;
        }

        .summary-item span:last-child {
            font-weight: 600;
            font-size: 14px;
            color: #242424;
        }

        .summary-item.total span:first-child {
            color: #242424;
            font-size: 16px;
            font-weight: 600;
        }

        .summary-item.total span:last-child {
            color: #fbb710;
            font-size: 18px;
            font-weight: 700;
        }

        .summary-item .text-success {
            color: #28a745 !important;
        }

        /* Shipping Address - Amado Style */
        .shipping-section {
            background-color: #f5f7fa;
            padding: 30px;
            border-left: 4px solid #fbb710;
            margin-bottom: 30px;
        }

        .shipping-section h4 {
            color: #242424;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .shipping-section p {
            color: #6d6d6d;
            font-size: 14px;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .shipping-section strong {
            color: #242424;
        }

        /* Next Steps - Amado Style */
        .next-steps-section {
            background-color: #e8f5e8;
            padding: 30px;
            border-left: 4px solid #28a745;
            margin-bottom: 30px;
        }

        .next-steps-section h4 {
            color: #28a745;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .next-steps-section p {
            color: #155724;
            font-size: 14px;
            margin-bottom: 0;
            line-height: 1.6;
        }

        /* Sidebar - Amado Style */
        .cart-summary {
            background-color: #f5f7fa;
            padding: 30px 20px;
            position: relative;
            z-index: 1;
            margin-bottom: 25px;
        }

        .cart-summary h5 {
            font-size: 18px;
            margin-bottom: 25px;
            color: #242424;
            font-weight: 600;
        }

        .summary-table {
            margin: 0;
            padding: 0;
        }

        .summary-table li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #6b6b6b;
        }

        .summary-table li:last-child {
            margin-bottom: 0;
        }

        .summary-table li span:first-child {
            color: #6d6d6d;
        }

        .summary-table li span:last-child {
            color: #242424;
            font-weight: 500;
        }

        /* Amado Button */
        .amado-btn {
            display: inline-block;
            min-width: 160px;
            height: 55px;
            color: #ffffff;
            border: none;
            border-radius: 0;
            padding: 0 20px;
            font-size: 16px;
            line-height: 55px;
            background-color: #fbb710;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 500ms ease 0s;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .amado-btn:hover,
        .amado-btn:focus {
            color: #ffffff;
            background-color: #131212;
            text-decoration: none;
        }

        /* Payment Instructions */
        .payment-instructions {
            margin-top: 25px;
        }

        .payment-instructions p {
            color: #242424;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .payment-instructions ul {
            padding-left: 20px;
            margin: 0;
        }

        .payment-instructions li {
            color: #6d6d6d;
            font-size: 13px;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .payment-instructions a {
            color: #fbb710;
            text-decoration: none;
            font-weight: 600;
        }

        .payment-instructions a:hover {
            color: #131212;
            text-decoration: underline;
        }

        /* Important Info Box */
        .important-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 0;
            padding: 20px;
            margin-top: 25px;
        }

        .important-info h6 {
            color: #856404;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 14px;
        }

        .important-info ul {
            margin: 0;
            padding-left: 15px;
        }

        .important-info li {
            color: #856404;
            font-size: 12px;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        /* Alert styling sesuai Amado */
        .alert {
            padding: 20px;
            margin-bottom: 25px;
            border: none;
            border-radius: 0;
            border-left: 4px solid;
        }

        .alert-success {
            color: #155724;
            background-color: #f8f9fa;
            border-left-color: #28a745;
        }

        .alert-info {
            color: #0c5460;
            background-color: #f8f9fa;
            border-left-color: #17a2b8;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8f9fa;
            border-left-color: #dc3545;
        }

        /* Responsive Design */
        @media (max-width: 768px) {

            .payment-header,
            .product-details-section,
            .payment-summary-section {
                padding: 20px;
                margin-bottom: 25px;
            }

            .payment-header h2 {
                font-size: 24px;
            }

            .product-table thead th,
            .product-table tbody td {
                padding: 10px 8px;
                font-size: 12px;
            }

            .product-image,
            .product-image-placeholder {
                width: 40px;
                height: 40px;
                margin-right: 10px;
            }

            .product-info h5 {
                font-size: 14px;
            }

            .amado-btn {
                width: 100%;
                margin-bottom: 15px;
                min-width: auto;
            }

            .summary-item {
                padding: 8px 0;
            }
        }

        /* Section padding sesuai template */
        .section-padding-10 {
            padding: 50px 0;
        }
    </style>
@endsection

@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="payment-full-page mt-50 clearfix">
                        <!-- Payment Header -->
                        <div class="payment-header">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div class="mb-3">
                                    <h2>Pembayaran Pelunasan Berhasil Dibuat</h2>
                                    <p>Produk Kustom: <strong>{{ $customRequest->title }}</strong></p>
                                </div>
                                <div class="mb-3">
                                    <span class="status-badge">
                                        <i class="fa fa-clock-o"></i>
                                        Menunggu Pelunasan
                                    </span>
                                </div>
                            </div>
                        </div>

                        @include('notifikasi.notifikasi')

                        <!-- Product Details Section -->
                        <div class="product-details-section">
                            <h3><i class="fa fa-cube"></i> Detail Produk Kustom Final</h3>
                            <div class="table-responsive">
                                <table class="product-table">
                                    <thead>
                                        <tr>
                                            <th>Produk Final</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Status</th>
                                            <th>Total Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $finalProducts = is_string($customRequest->final_product_images)
                                                            ? json_decode($customRequest->final_product_images, true)
                                                            : $customRequest->final_product_images;

                                                        $referenceImage = $customRequest->references()->first();
                                                    @endphp

                                                    @if ($finalProducts && is_array($finalProducts) && count($finalProducts) > 0)
                                                        {{-- Tampilkan foto produk final --}}
                                                        <img src="{{ asset('storage/' . $finalProducts[0]['image']) }}"
                                                            alt="{{ $customRequest->title }}" class="product-image">
                                                    @elseif ($referenceImage && $referenceImage->image_path)
                                                        {{-- Fallback ke foto referensi --}}
                                                        <img src="{{ asset('storage/' . $referenceImage->image_path) }}"
                                                            alt="{{ $customRequest->title }}" class="product-image"
                                                            style="border-color: #fbb710;">
                                                    @elseif ($customRequest->image)
                                                        <img src="{{ asset('storage/' . $customRequest->image) }}"
                                                            alt="{{ $customRequest->title }}" class="product-image"
                                                            style="border-color: #fbb710;">
                                                    @else
                                                        <div class="product-image-placeholder">
                                                            <i class="fa fa-check-circle"
                                                                style="color: #28a745; font-size: 24px;"></i>
                                                        </div>
                                                    @endif

                                                    <div class="product-info">
                                                        <h5>{{ $customRequest->title }}</h5>
                                                        @if ($finalProducts && is_array($finalProducts) && count($finalProducts) > 0)
                                                            <small style="color: #28a745; font-weight: 600;">
                                                                <i class="fa fa-check-circle"></i> Produk Selesai
                                                            </small>
                                                        @else
                                                            <small style="color: #6d6d6d;">Produk Kustom</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $customRequest->updated_at->format('d M Y') }}</td>
                                            <td>
                                                <span class="product-status">Selesai</span>
                                            </td>
                                            <td style="font-weight: 600; color: #fbb710;">
                                                Rp {{ number_format($customRequest->quoted_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="payment-summary-section">
                            <h4><i class="fa fa-calculator"></i> Ringkasan Pembayaran Pelunasan</h4>

                            <div class="summary-item">
                                <span>Total Harga Produk</span>
                                <span>Rp {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</span>
                            </div>

                            <div class="summary-item">
                                <span>DP yang Sudah Dibayar</span>
                                <span class="text-success">- Rp
                                    {{ number_format($customRequest->down_payment, 0, ',', '.') }}</span>
                            </div>

                            <div class="summary-item">
                                <span>Sisa Pembayaran</span>
                                <span>Rp {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</span>
                            </div>

                            <div class="summary-item">
                                <span>Biaya Pengiriman</span>
                                <span>Rp
                                    {{ number_format($customRequest->shipment->shipping_cost ?? 0, 0, ',', '.') }}</span>
                            </div>

                            <div class="summary-item total">
                                <span>Total Pelunasan</span>
                                <span>Rp
                                    {{ number_format($customRequest->remaining_payment + ($customRequest->shipment->shipping_cost ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="shipping-section">
                            <h4><i class="fa fa-truck"></i> Alamat Pengiriman</h4>
                            <p><strong>{{ $customRequest->shipment->full_name }}</strong></p>
                            <p>{{ $customRequest->shipment->address }}</p>
                            <p>{{ $customRequest->shipment->city->name }}, {{ $customRequest->shipment->province->name }}
                                {{ $customRequest->shipment->postal_code }}</p>
                            <p>{{ $customRequest->shipment->phone }}</p>
                            <p>{{ $customRequest->shipment->email }}</p>

                            @if ($customRequest->shipment->notes)
                                <hr style="margin: 15px 0; border-color: #dee2e6;">
                                <p><strong>Catatan Pengiriman:</strong> {{ $customRequest->shipment->notes }}</p>
                            @endif
                        </div>

                        <!-- Next Steps -->
                        <div class="next-steps-section">
                            <h4><i class="fa fa-info-circle"></i> Langkah Selanjutnya</h4>
                            <p>Setelah pembayaran pelunasan, produk kustom Anda akan segera dikirim ke alamat yang telah
                                ditentukan. Nomor resi pengiriman akan ditambahkan ke halaman detail permintaan dan Anda
                                akan mendapat notifikasi via email.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <!-- Payment Information -->
                    <div class="cart-summary">
                        <h5><i class="fa fa-credit-card"></i> Informasi Pembayaran</h5>
                        <ul class="summary-table">
                            <li><span>Metode:</span> <span>Virtual Account (Transfer Bank)</span></li>
                            <li><span>Status:</span> <span>Menunggu Pelunasan</span></li>
                            <li><span>Waktu:</span> <span>24 Jam</span></li>
                        </ul>
                        <p style="font-size: 14px; margin-top: 20px; color: #6d6d6d; line-height: 1.6;">
                            Silakan selesaikan pembayaran pelunasan melalui virtual account bank yang tersedia (BCA, BNI,
                            BRI, Mandiri, Permata).
                        </p>
                    </div>

                    <!-- Payment Action -->
                    <div class="cart-summary">
                        <h5><i class="fa fa-money"></i> Selesaikan Pelunasan</h5>
                        <p style="font-size: 14px; margin-bottom: 20px; color: #6d6d6d; line-height: 1.6;">
                            Silakan selesaikan pembayaran pelunasan untuk memproses pengiriman produk kustom Anda.
                        </p>

                        @isset($snapToken)
                            <button id="pay-button" class="amado-btn w-100">
                                <i class="fa fa-credit-card"></i> Bayar Pelunasan Sekarang
                            </button>
                        @else
                            <a href="{{ route('custom.show', $customRequest->id) }}" class="amado-btn w-100"
                                style="text-decoration: none;">
                                <i class="fa fa-arrow-left"></i> Kembali ke Detail Permintaan
                            </a>
                        @endisset

                        <div class="payment-instructions">
                            <p>Instruksi Pembayaran:</p>
                            <ul>
                                <li>Klik tombol "Bayar Pelunasan Sekarang" di atas</li>
                                <li>Pilih metode pembayaran (Transfer Bank / Virtual Account)</li>
                                <li>Ikuti instruksi pembayaran yang ditampilkan</li>
                                <li>Pengiriman akan dilakukan 1-2 hari kerja setelah pelunasan</li>
                                <li>Anda dapat melihat status pengiriman di
                                    <a href="{{ route('custom.show', $customRequest->id) }}">Detail Permintaan</a>
                                </li>
                            </ul>
                        </div>

                        <div class="important-info">
                            <h6><i class="fa fa-exclamation-triangle"></i> Informasi Penting</h6>
                            <ul>
                                <li>Pengiriman akan dilakukan 1-2 hari kerja setelah pelunasan</li>
                                <li>Nomor resi akan diberikan setelah produk dikirim</li>
                                <li>Pembatalan tidak dapat dilakukan pada tahap ini</li>
                                <li>Harap konfirmasi penerimaan produk setelah barang diterima</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @isset($snapToken)
        <!-- Midtrans -->
        <script type="text/javascript">
            // Dynamically load the correct Midtrans script based on environment
            const midtransScriptUrl =
                "{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}";
            const midtransClientKey = "{{ config('midtrans.client_key') }}";

            // Create the script element
            const midtransScript = document.createElement('script');
            midtransScript.src = midtransScriptUrl;
            midtransScript.setAttribute('data-client-key', midtransClientKey);
            document.body.appendChild(midtransScript);

            // Initialize the pay button after the script is loaded
            midtransScript.onload = function() {
                document.getElementById('pay-button').onclick = function() {
                    // SnapToken from controller
                    window.snap.pay('{{ $snapToken }}', {
                        onSuccess: function(result) {
                            /* Redirect to full payment success page */
                            window.location.href =
                                '{{ route('custom.payment-full-success', $customRequest->id) }}';
                        },
                        onPending: function(result) {
                            /* Redirect to custom request page with pending status */
                            window.location.href =
                                '{{ route('custom.show', $customRequest->id) }}?payment=pending';
                        },
                        onError: function(result) {
                            /* Show error message */
                            alert('Terjadi kesalahan pada proses pembayaran. Silakan coba lagi.');
                            console.log(result);
                        },
                        onClose: function() {
                            /* Payment cancelled */
                            alert('Anda menutup jendela pembayaran tanpa menyelesaikan pelunasan');
                        }
                    });
                };
            };
        </script>
    @endisset
@endpush
