@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <!-- Order Header Section -->
                        <div class="cart-title">
                            <h2>Pembayaran DP Berhasil Dibuat</h2>
                        </div>
                        <p style="font-size: 14px;">Produk Kustom: <strong>{{ $customRequest->title }}</strong></p>
                        <p style="font-size: 14px;">Status: <span class="badge"
                                style="background-color: #fbb710; color: #fff; font-weight: 400; font-size: 12px; padding: 5px 10px;">Menunggu
                                Pembayaran DP</span>
                        </p>

                        @include('notifikasi.notifikasi')

                        <!-- Custom Request Details Section -->
                        <div class="row mb-4 mt-30">
                            <div class="col-12">
                                <div class="order-items-card">
                                    <h3 style="font-size: 18px; margin-bottom: 15px;">Detail Permintaan Kustom</h3>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Tanggal Permintaan</th>
                                                    <th>Estimasi Selesai</th>
                                                    <th>Total Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @php
                                                                $referenceImage = $customRequest->references()->first();
                                                            @endphp

                                                            @if ($referenceImage && $referenceImage->image_path)
                                                                <img src="{{ asset('storage/' . $referenceImage->image_path) }}"
                                                                    alt="{{ $customRequest->title }}"
                                                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 4px;">
                                                            @elseif ($customRequest->image)
                                                                <img src="{{ asset('storage/' . $customRequest->image) }}"
                                                                    alt="{{ $customRequest->title }}"
                                                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 4px;">
                                                            @else
                                                                <div
                                                                    style="width: 50px; height: 50px; background-color: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; margin-right: 10px; border-radius: 4px;">
                                                                    <i class="fa fa-cogs"
                                                                        style="color: #fbb710; font-size: 20px;"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <p class="mb-0" style="font-weight: 500;">
                                                                    {{ $customRequest->title }}</p>
                                                                @if ($referenceImage && $referenceImage->description)
                                                                    <small
                                                                        class="text-muted">{{ Str::limit($referenceImage->description, 30) }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $customRequest->created_at->format('d M Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($customRequest->estimated_completion)->format('d M Y') }}
                                                    </td>
                                                    <td>Rp {{ number_format($customRequest->quoted_price, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary Table - IMPROVED STYLING -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="order-summary-card"
                                    style="border: 1px solid #e8e8e8; border-radius: 5px; padding: 20px; background-color: #fff;">
                                    <h4 style="font-size: 18px; margin-bottom: 20px; color: #242424;">Ringkasan Pembayaran
                                        DP
                                    </h4>

                                    <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Total Harga Produk</span>
                                        <span style="font-size: 14px; font-weight: 500;">Rp
                                            {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Jumlah DP (50%)</span>
                                        <span style="font-size: 14px; font-weight: 500;">Rp
                                            {{ number_format($customRequest->down_payment, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Sisa Pembayaran</span>
                                        <span style="font-size: 14px; font-weight: 500;">Rp
                                            {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</span>
                                    </div>

                                    <hr style="margin: 15px 0; border-color: #f0f0f0;">

                                    <div class="summary-item d-flex justify-content-between align-items-center">
                                        <span style="font-size: 16px; font-weight: 600; color: #242424;">Total DP yang Harus
                                            Dibayar</span>
                                        <span style="font-size: 18px; font-weight: 700; color: #fbb710;">Rp
                                            {{ number_format($customRequest->down_payment, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="shipping-address-card"
                                    style="background-color: #f5f7fa; padding: 20px; border-radius: 5px;">
                                    <h4 style="font-size: 18px; margin-bottom: 15px;">Informasi Penting</h4>
                                    <p style="font-size: 14px; margin-bottom: 8px;">
                                        <strong>Langkah Selanjutnya:</strong>
                                    </p>
                                    <p style="font-size: 14px; margin-bottom: 8px;">Setelah pembayaran DP, kami akan mulai
                                        mengerjakan produk kustom Anda. Progress pengerjaan akan diperbarui secara berkala
                                        di halaman detail permintaan.</p>
                                    <p style="font-size: 14px; margin-bottom: 8px;">
                                        <strong>Catatan:</strong>
                                    </p>
                                    <ul style="font-size: 14px; margin-left: 15px;">
                                        <li>Pembayaran DP tidak dapat dikembalikan</li>
                                        <li>Proses pengerjaan dimulai setelah DP diterima</li>
                                        <li>Estimasi waktu pengerjaan dihitung sejak DP diterima</li>
                                        <li>Status pengerjaan dapat dipantau di halaman detail permintaan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <!-- Informasi Pembayaran Section -->
                    <div class="cart-summary" style="margin-bottom: 20px; border-bottom: none; border-radius: 5px 5px 0 0;">
                        <h5>Informasi Pembayaran</h5>
                        <ul class="summary-table">
                            <li><span>Metode:</span> <span>Virtual Account (Transfer Bank)</span></li>
                            <li><span>Status:</span> <span>Menunggu Pembayaran DP</span></li>
                            <li><span>Waktu:</span> <span>24 Jam</span></li>
                        </ul>
                        <div class="payment-method">
                            <p style="font-size: 14px; margin-top: 20px;">Silakan selesaikan pembayaran DP melalui virtual
                                account bank yang tersedia (BCA, BNI, BRI, Mandiri, Permata).</p>
                        </div>
                    </div>

                    <!-- Payment Instruction Section - WITH SEPARATE BORDER -->
                    <div class="cart-summary"
                        style="margin-top: 0; border-top: 1px solid #ebebeb; border-radius: 0 0 5px 5px;">
                        <h5>Selesaikan Pembayaran DP</h5>
                        <p style="font-size: 14px; margin-bottom: 20px;">Silakan selesaikan pembayaran DP Anda untuk
                            memulai proses pengerjaan produk kustom.</p>

                        @isset($snapToken)
                            <button id="pay-button" class="amado-btn w-100">Bayar DP Sekarang</button>
                        @else
                            <a href="{{ route('custom.show', $customRequest->id) }}" class="amado-btn w-100 text-center"
                                style="display: block; text-decoration: none;">Kembali ke Detail Permintaan</a>
                        @endisset

                        <div class="payment-notes mt-30">
                            <p style="font-size: 14px; margin-bottom: 10px;">Instruksi Pembayaran:</p>
                            <ul style="padding-left: 20px;">
                                <li style="font-size: 13px; margin-bottom: 5px; color: #6d6d6d;">Klik tombol "Bayar DP
                                    Sekarang" di atas</li>
                                <li style="font-size: 13px; margin-bottom: 5px; color: #6d6d6d;">Pilih metode pembayaran
                                    (Transfer Bank / Virtual Account)</li>
                                <li style="font-size: 13px; margin-bottom: 5px; color: #6d6d6d;">Ikuti instruksi pembayaran
                                    yang ditampilkan</li>
                                <li style="font-size: 13px; margin-bottom: 5px; color: #6d6d6d;">Setelah pembayaran
                                    berhasil, status akan diperbarui otomatis</li>
                                <li style="font-size: 13px; color: #6d6d6d;">Anda dapat melihat status permintaan di
                                    <a href="{{ route('custom.show', $customRequest->id) }}"
                                        style="color: #fbb710; text-decoration: none;">Detail Permintaan</a>
                                </li>
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
                            /* Redirect to DP success page */
                            window.location.href =
                                '{{ route('custom.payment-dp-success', $customRequest->id) }}';
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
                            alert('Anda menutup jendela pembayaran tanpa menyelesaikan pembayaran DP');
                        }
                    });
                };
            };
        </script>
    @endisset
@endpush
