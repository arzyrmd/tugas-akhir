<!-- resources/views/payment/error.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="payment-error-area section-padding-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="payment-error-box text-center">
                        <i class="fa fa-times-circle text-danger fa-5x mb-4"></i>
                        <h2>Pembayaran Gagal</h2>
                        <p>{{ $errorMessage }}</p>

                        <div class="order-info mt-4">
                            <h4>Informasi Pesanan</h4>
                            <p>Nomor Pesanan: <strong>{{ $order->payment_code }}</strong></p>
                            <p>Total Pembayaran: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>
                            <p>Status:
                                <span class="badge"
                                    style="background-color: #ffc107; color: #212529; font-size: 12px; padding: 5px 8px; font-weight: 500; border-radius: 3px;">
                                    {{ $order->status }}
                                </span>
                            </p>
                            <p>Metode Pembayaran: <strong>Virtual Account (Transfer Bank)</strong></p>
                        </div>

                        <div class="mt-4">
                            <p>Anda dapat mencoba melakukan pembayaran kembali menggunakan Virtual Account (Transfer Bank).
                            </p>
                            <p>Jika masalah berlanjut, silakan hubungi layanan pelanggan kami.</p>
                        </div>

                        <div class="mt-5">
                            <a href="{{ route('home') }}" class="btn"
                                style="background-color: #f5f7fa; color: #333; border: 1px solid #ddd; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-right: 10px;">
                                Kembali ke Beranda
                            </a>
                            <a href="{{ route('account.orders') }}" class="btn"
                                style="background-color: #f5f7fa; color: #333; border: 1px solid #ddd; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-right: 10px;">
                                Lihat Pesanan Saya
                            </a>
                            <button id="retry-payment" class="btn"
                                style="background-color: #fbb710; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                                <span id="retry-payment-text">Coba Bayar Lagi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Notification Container -->
    <div id="notification-container" style="position: fixed; top: 20px; right: 20px; max-width: 350px; z-index: 9999;">
    </div>
@endsection

@push('scripts')
    <script>
        // Function to show notification messages
        function showNotification(message, type) {
            // Create notification element
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
            notification.style.transform = 'translateY(-20px)';
            notification.style.transition = 'all 0.3s ease-in-out';

            // Add icon
            const icon = document.createElement('i');
            icon.className = type === 'success' ? 'fa fa-check-circle mr-2' : 'fa fa-exclamation-circle mr-2';
            notification.appendChild(icon);

            // Add message text
            const text = document.createTextNode(message);
            notification.appendChild(text);

            // Add to container
            const container = document.getElementById('notification-container');
            container.appendChild(notification);

            // Animation in
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateY(0)';
            }, 100);

            // Remove after a few seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

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
            document.getElementById('retry-payment').onclick = function() {
                // Show loading state
                const retryButton = document.getElementById('retry-payment');
                const buttonText = document.getElementById('retry-payment-text');
                const originalText = buttonText.innerText;

                buttonText.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memuat...';
                retryButton.disabled = true;

                // Get new snap token
                fetch('{{ route('payment.retry', ['order_id' => $order->id]) }}')
                    .then(response => response.json())
                    .then(data => {
                        // Reset button state
                        buttonText.innerText = originalText;
                        retryButton.disabled = false;

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
                                    showNotification(result.status_message ||
                                        'Terjadi kesalahan pada pembayaran', 'error');
                                },
                                onClose: function() {
                                    showNotification(
                                        'Anda menutup jendela pembayaran tanpa menyelesaikan pembayaran',
                                        'error');
                                }
                            });
                        } else {
                            showNotification(data.error ||
                                'Gagal mendapatkan token pembayaran. Silakan coba lagi nanti.', 'error');
                        }
                    })
                    .catch(error => {
                        // Reset button state
                        buttonText.innerText = originalText;
                        retryButton.disabled = false;

                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan. Silakan coba lagi nanti.', 'error');
                    });
            };
        };
    </script>
@endpush
