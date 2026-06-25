<!-- resources/views/account/order-detail.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <!-- Order Header Section -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="cart-title">
                                <h2>Detail Pesanan</h2>
                            </div>
                            <a href="{{ route('account.orders') }}" class="btn"
                                style="background-color: #fbb710; color: #fff; border: none; font-size: 13px; border-radius: 4px; padding: 8px 15px;">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>

                        <p style="font-size: 14px;">Nomor Pesanan: <strong>{{ $order->payment_code }}</strong></p>
                        <p style="font-size: 14px;">Status:
                            @if ($order->status == 'MENUNGGU PEMBAYARAN')
                                <span class="badge"
                                    style="background-color: #ffc107; color: #212529; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                            @elseif($order->status == 'PEMBAYARAN BERHASIL')
                                <span class="badge"
                                    style="background-color: #17a2b8; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                            @elseif($order->status == 'DIKEMAS')
                                <span class="badge"
                                    style="background-color: #007bff; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                            @elseif($order->status == 'DIKIRIM')
                                <span class="badge"
                                    style="background-color: #6c757d; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                            @elseif($order->status == 'SELESAI')
                                <span class="badge"
                                    style="background-color: #28a745; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                            @elseif($order->status == 'DIBATALKAN')
                                <span class="badge"
                                    style="background-color: #dc3545; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                            @endif
                        </p>

                        <!-- Custom Notification Section -->
                        <div id="notification-container"
                            style="position: fixed; top: 20px; left: 20px; max-width: 350px; z-index: 9999;"></div>

                        @if (session('show_refund_info'))
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div
                                        style="background-color: #d1ecf1; color: #0c5460; border-radius: 5px; padding: 15px; border-left: 4px solid #0c5460;">
                                        <p style="font-size: 14px; margin-bottom: 10px;"><strong>Informasi Pengembalian
                                                Dana:</strong></p>
                                        <p style="font-size: 14px; margin-bottom: 10px;">Untuk proses pengembalian dana,
                                            silakan hubungi admin kami melalui WhatsApp:
                                            <a href="https://wa.me/628123456789" target="_blank"
                                                style="display: inline-flex; align-items: center; color: #0c5460; text-decoration: none; font-weight: 500;">
                                                <i class="fa fa-whatsapp mr-1" style="color: #25D366; font-size: 16px;"></i>
                                                +62 8123-4567-89
                                            </a>
                                        </p>
                                        <p style="font-size: 14px; margin-bottom: 0;">Mohon sertakan nomor pesanan Anda:
                                            <strong>{{ $order->payment_code }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Order Items Section -->
                        <div class="row mb-4 mt-30">
                            <div class="col-12">
                                <div class="order-items-card">
                                    <h4 style="font-size: 18px; margin-bottom: 15px;">Produk yang Dipesan</h4>
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

                        <!-- Order Summary Table -->
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

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Metode Pembayaran:</span>
                                        <span
                                            style="font-size: 14px; font-weight: 500;">{{ $order->payment_method ?: 'Virtual Account (Transfer Bank)' }}</span>
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
                                    @if ($order->notes)
                                        <p style="font-size: 14px; margin-top: 15px; color: #6d6d6d;">
                                            <strong>Catatan:</strong> {{ $order->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR COLUMN -->
                <div class="col-12 col-lg-4">
                    <!-- Order Info Summary -->
                    <div class="cart-summary">
                        <h5>Informasi Pesanan</h5>
                        <ul class="summary-table">
                            <li><span>No. Pesanan:</span> <span>{{ $order->payment_code }}</span></li>
                            <li><span>Status:</span>
                                <span>
                                    @if ($order->status == 'MENUNGGU PEMBAYARAN')
                                        <span class="badge"
                                            style="background-color: #ffc107; color: #212529; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @elseif($order->status == 'PEMBAYARAN BERHASIL')
                                        <span class="badge"
                                            style="background-color: #17a2b8; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @elseif($order->status == 'DIKEMAS')
                                        <span class="badge"
                                            style="background-color: #007bff; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @elseif($order->status == 'SIAP DIKIRIM')
                                        <span class="badge"
                                            style="background-color: #6610f2; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @elseif($order->status == 'DIKIRIM')
                                        <span class="badge"
                                            style="background-color: #6c757d; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @elseif($order->status == 'SELESAI')
                                        <span class="badge"
                                            style="background-color: #28a745; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @elseif($order->status == 'DIBATALKAN')
                                        <span class="badge"
                                            style="background-color: #dc3545; color: white; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">{{ $order->status }}</span>
                                    @endif
                                </span>
                            </li>
                            <li><span>Tanggal Pemesanan:</span>
                                <span>{{ $order->order_created_at->format('d M Y H:i') }}</span>
                            </li>
                            @if ($order->payment_date)
                                <li><span>Tanggal Pembayaran:</span>
                                    <span>{{ $order->payment_date->format('d M Y H:i') }}</span>
                                </li>
                            @endif
                            @if ($order->packing_date)
                                <li><span>Tanggal Dikemas:</span>
                                    <span>{{ $order->packing_date->format('d M Y H:i') }}</span>
                                </li>
                            @endif
                            @if ($order->delivery_date)
                                <li><span>Tanggal Pengiriman:</span>
                                    <span>{{ $order->delivery_date->format('d M Y H:i') }}</span>
                                </li>
                            @endif
                            @if ($order->delivery_date)
                                <li><span>Tanggal Pengiriman:</span>
                                    <span>{{ $order->delivery_date->format('d M Y') }}</span>
                                    <!-- Hapus H:i jika tidak ingin menampilkan waktu -->
                                </li>
                            @endif
                        </ul>

                        <!-- Action Buttons (No Title) -->
                        @if (in_array($order->status, ['MENUNGGU PEMBAYARAN', 'PEMBAYARAN BERHASIL', 'DIKEMAS']))
                            <div class="mt-4 mb-2">
                                @if ($order->status == 'MENUNGGU PEMBAYARAN')
                                    <button id="pay-button" class="btn w-100 mb-3"
                                        style="background-color: #fbb710; color: #fff; border: none; border-radius: 4px; padding: 10px 15px; font-size: 14px; font-weight: 500; display: flex; justify-content: center; align-items: center;">
                                        <i class="fa fa-credit-card mr-2"></i> Bayar Sekarang
                                    </button>
                                @endif

                                <form action="{{ route('account.orders.cancel', $order) }}" method="POST"
                                    class="cancel-form w-100">
                                    @csrf
                                    <button type="submit" class="btn w-100"
                                        style="background-color: #dc3545; color: #fff; border: none; border-radius: 4px; padding: 10px 15px; font-size: 14px; font-weight: 500; display: flex; justify-content: center; align-items: center;">
                                        <i class="fa fa-times-circle mr-2"></i> Batalkan Pesanan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Customer Support -->
                    <div class="cart-summary" style="margin-top: 20px;">
                        <h5>Butuh Bantuan?</h5>
                        <div class="support-info">
                            <p style="font-size: 14px; margin-bottom: 15px;">Jika Anda memiliki pertanyaan tentang pesanan
                                ini, silakan hubungi tim layanan pelanggan kami.</p>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-envelope-o mr-2" style="color: #fbb710; width: 20px;"></i>
                                <span style="font-size: 14px;">support@example.com</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-whatsapp mr-2" style="color: #25D366; width: 20px; font-size: 16px;"></i>
                                <a href="https://wa.me/628123456789" target="_blank"
                                    style="font-size: 14px; color: #333; text-decoration: none;">+62 8123-4567-89</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-clock-o mr-2" style="color: #fbb710; width: 20px;"></i>
                                <span style="font-size: 14px;">Senin - Jumat, 08:00 - 17:00 WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Fungsi untuk menampilkan notifikasi kustom
            function showNotification(message, type) {
                // Buat elemen notifikasi
                const notification = document.createElement('div');
                notification.className = 'custom-notification';
                notification.style.backgroundColor = type === 'success' ? '#d4edda' : '#f8d7da';
                notification.style.color = type === 'success' ? '#155724' : '#721c24';
                notification.style.borderLeft = type === 'success' ? '4px solid #28a745' : '4px solid #dc3545';
                notification.style.padding = '15px 20px';
                notification.style.borderRadius = '5px';
                notification.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                notification.style.marginBottom = '10px';
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(-100%)';
                notification.style.transition = 'all 0.3s ease-in-out';

                // Tambahkan ikon
                const icon = document.createElement('i');
                icon.className = type === 'success' ? 'fa fa-check-circle mr-2' : 'fa fa-exclamation-circle mr-2';
                notification.appendChild(icon);

                // Tambahkan teks pesan
                const text = document.createTextNode(message);
                notification.appendChild(text);

                // Tambahkan ke container
                const container = document.getElementById('notification-container');
                container.appendChild(notification);

                // Animasi masuk
                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateX(0)';
                }, 100);

                // Hapus setelah beberapa detik
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 5000);
            }

            // Cek apakah ada session flash success atau error
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    showNotification("{{ session('success') }}", 'success');
                    // Hapus alert default
                    const successAlert = document.querySelector('.alert.alert-success');
                    if (successAlert) successAlert.style.display = 'none';
                @endif

                @if (session('error'))
                    showNotification("{{ session('error') }}", 'error');
                    // Hapus alert default
                    const errorAlert = document.querySelector('.alert.alert-danger');
                    if (errorAlert) errorAlert.style.display = 'none';
                @endif

                // Proses form pembatalan
                const cancelForm = document.querySelector('.cancel-form');
                if (cancelForm) {
                    cancelForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
                            this.submit();
                        }
                    });
                }
            });
        </script>

        @if ($order->status == 'MENUNGGU PEMBAYARAN')
            <!-- Midtrans Dynamic Script Loading -->
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
                        // Show loading state on button
                        const payButton = document.getElementById('pay-button');
                        const originalText = payButton.innerHTML;
                        payButton.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Memuat Pembayaran...';
                        payButton.disabled = true;

                        // Fetch new snap token
                        fetch('{{ route('payment.retry', ['order_id' => $order->id]) }}')
                            .then(response => response.json())
                            .then(data => {
                                // Reset button state
                                payButton.innerHTML = originalText;
                                payButton.disabled = false;

                                if (data.snap_token) {
                                    window.snap.pay(data.snap_token, {
                                        onSuccess: function(result) {
                                            window.location.href =
                                                '{{ route('payment.success', ['order_id' => $order->id]) }}' +
                                                '?transaction_id=' + result.transaction_id;
                                        },
                                        onPending: function(result) {
                                            window.location.href =
                                                '{{ route('payment.pending', ['order_id' => $order->id]) }}' +
                                                '?transaction_id=' + result.transaction_id;
                                        },
                                        onError: function(result) {
                                            window.location.href =
                                                '{{ route('payment.error', ['order_id' => $order->id]) }}' +
                                                '?message=' + result.status_message;
                                        },
                                        onClose: function() {
                                            alert(
                                                'Anda menutup jendela pembayaran tanpa menyelesaikan pembayaran'
                                            );
                                        }
                                    });
                                } else {
                                    showNotification(data.error ||
                                        'Gagal mendapatkan token pembayaran. Silakan coba lagi nanti.', 'error');
                                }
                            })
                            .catch(error => {
                                // Reset button state
                                payButton.innerHTML = originalText;
                                payButton.disabled = false;

                                console.error('Error:', error);
                                showNotification('Terjadi kesalahan. Silakan coba lagi nanti.', 'error');
                            });
                    };
                };
            </script>
        @endif
    @endpush
@endsection
