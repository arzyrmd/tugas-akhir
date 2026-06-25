<!-- resources/views/checkout/payment.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <!-- Order Header Section -->
                        <div class="cart-title">
                            <h2>Pesanan Berhasil Dibuat</h2>
                        </div>
                        <p style="font-size: 14px;">Nomor Pesanan: <strong>{{ $order->payment_code }}</strong></p>
                        <p style="font-size: 14px;">Status: <span class="badge"
                                style="background-color: #fbb710; color: #fff; font-weight: 400; font-size: 12px; padding: 5px 10px;">{{ $order->status }}</span>
                        </p>

                        <!-- Order Items Section -->
                        <div class="row mb-4 mt-30">
                            <div class="col-12">
                                <div class="order-items-card">
                                    <h3 style="font-size: 18px; margin-bottom: 15px;">Ringkasan Pesanan</h3>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Kategori</th>
                                                    <th>Harga</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderItems as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if ($item->product->image)
                                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                        alt="{{ $item->product->name }}"
                                                                        style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                                @else
                                                                    <img src="{{ asset('img/no-image.png') }}"
                                                                        alt="No Image Available"
                                                                        style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                                @endif
                                                                <p class="mb-0">{{ $item->product->name }}</p>
                                                            </div>
                                                        </td>
                                                        <td>{{ $item->product->category->name }}</td>
                                                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                        <td>{{ $item->quantity }}</td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary Table - IMPROVED STYLING -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="order-summary-card"
                                    style="border: 1px solid #e8e8e8; border-radius: 5px; padding: 20px; background-color: #fff;">
                                    <h4 style="font-size: 18px; margin-bottom: 20px; color: #242424;">Ringkasan Pembayaran
                                    </h4>

                                    <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Subtotal</span>
                                        <span style="font-size: 14px; font-weight: 500;">Rp
                                            {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Biaya Pengiriman</span>
                                        <span style="font-size: 14px; font-weight: 500;">Rp
                                            {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                    </div>

                                    <hr style="margin: 15px 0; border-color: #f0f0f0;">

                                    <div class="summary-item d-flex justify-content-between align-items-center">
                                        <span style="font-size: 16px; font-weight: 600; color: #242424;">Total</span>
                                        <span style="font-size: 18px; font-weight: 700; color: #fbb710;">Rp
                                            {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="shipping-address-card"
                                    style="background-color: #f5f7fa; padding: 20px; border-radius: 5px;">
                                    <h4 style="font-size: 18px; margin-bottom: 15px;">Alamat Pengiriman</h4>
                                    <p style="font-size: 14px; margin-bottom: 8px;">
                                        <strong>{{ $order->full_name }}</strong>
                                    </p>
                                    <p style="font-size: 14px; margin-bottom: 8px;">{{ $order->address }}</p>
                                    <p style="font-size: 14px; margin-bottom: 8px;">{{ $order->city->name }},
                                        {{ $order->province->name }} {{ $order->postal_code }}</p>
                                    <p style="font-size: 14px; margin-bottom: 0;">{{ $order->phone }}</p>
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
                            <li><span>Status:</span> <span>Menunggu Pembayaran</span></li>
                            <li><span>Waktu:</span> <span>24 Jam</span></li>
                        </ul>
                        <div class="payment-method">
                            <p style="font-size: 14px; margin-top: 20px;">Silakan selesaikan pembayaran melalui virtual
                                account bank yang tersedia (BCA, BNI, BRI, Mandiri, Permata).</p>
                        </div>
                    </div>

                    <!-- Payment Instruction Section - WITH SEPARATE BORDER -->
                    <div class="cart-summary"
                        style="margin-top: 0; border-top: 1px solid #ebebeb; border-radius: 0 0 5px 5px;">
                        <h5>Selesaikan Pembayaran</h5>
                        <p style="font-size: 14px; margin-bottom: 20px;">Silakan selesaikan pembayaran Anda untuk
                            melanjutkan proses pesanan.</p>

                        <button id="pay-button" class="amado-btn w-100">Bayar Sekarang</button>

                        <div class="payment-notes mt-30">
                            <p style="font-size: 14px; margin-bottom: 10px;">Catatan:</p>
                            <ul style="padding-left: 20px;">
                                <li style="font-size: 13px; margin-bottom: 5px; color: #6d6d6d;">Pembayaran harus
                                    dilakukan dalam waktu 24 jam</li>
                                <li style="font-size: 13px; margin-bottom: 5px; color: #6d6d6d;">Pesanan Anda akan
                                    diproses setelah pembayaran dikonfirmasi</li>
                                <li style="font-size: 13px; color: #6d6d6d;">Anda dapat melihat status pesanan di
                                    halaman
                                    <a href="{{ route('account.orders') }}"
                                        style="color: #fbb710; text-decoration: none;">Pesanan Saya</a>
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
                        /* Redirect to success page */
                        window.location.href =
                            '{{ route('payment.success', ['order_id' => $order->id]) }}' +
                            '?transaction_id=' + result.transaction_id;
                    },
                    onPending: function(result) {
                        /* Redirect to pending page */
                        window.location.href =
                            '{{ route('payment.pending', ['order_id' => $order->id]) }}' +
                            '?transaction_id=' + result.transaction_id;
                    },
                    onError: function(result) {
                        /* Redirect to error page */
                        window.location.href =
                            '{{ route('payment.error', ['order_id' => $order->id]) }}' +
                            '?message=' + result.status_message;
                    },
                    onClose: function() {
                        /* Payment cancelled, but order still exists */
                        alert('Anda menutup jendela pembayaran tanpa menyelesaikan pembayaran');
                    }
                });
            };
        };
    </script>
@endpush
