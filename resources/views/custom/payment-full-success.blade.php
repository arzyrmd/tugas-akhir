{{-- resources/views/custom/payment-full-success.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .success-page {
            margin-bottom: 30px;
        }

        .success-header {
            text-align: center;
            padding: 40px 0;
            border-bottom: 1px solid #ebebeb;
            margin-bottom: 30px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon i {
            font-size: 40px;
            color: white;
        }

        .payment-receipt {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .receipt-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1em;
            color: #28a745;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875em;
            font-weight: 500;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-ready {
            background-color: #cce7ff;
            color: #004085;
            border: 1px solid #b6d4fe;
        }

        .next-steps {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin-bottom: 20px;
        }

        .shipping-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-group .btn {
            flex: 1;
            min-width: 150px;
        }

        .invoice-section {
            background-color: #fff8e1;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-invoice {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
            font-weight: 500;
        }

        .btn-invoice:hover {
            background-color: #ffca2c;
            border-color: #ffc720;
            color: #212529;
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="success-page mt-50">
                        <div class="success-header">
                            <div class="success-icon">
                                <i class="fa fa-check"></i>
                            </div>
                            <h2>Pembayaran Pelunasan Berhasil!</h2>
                            <p class="lead">Terima kasih! Pembayaran pelunasan untuk produk kustom Anda telah berhasil
                                diproses.</p>
                        </div>

                        {{-- Invoice Download Section --}}
                        <div class="invoice-section">
                            <h5><i class="fa fa-file-text"></i> Invoice Pembayaran</h5>
                            <p class="mb-3">Download invoice resmi untuk pembayaran pelunasan Anda sebagai bukti
                                transaksi.</p>
                            <a href="{{ route('invoice.custom.full', $customRequest->id) }}" class="btn btn-invoice"
                                target="_blank">
                                <i class="fa fa-download"></i> Download Invoice PDF
                            </a>
                        </div>

                        <div class="payment-receipt">
                            <h4 class="mb-4">
                                <i class="fa fa-receipt"></i> Bukti Pembayaran Pelunasan
                            </h4>

                            <div class="receipt-row">
                                <span>Nomor Transaksi:</span>
                                <strong>{{ $customRequest->full_payment_code }}</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Produk:</span>
                                <span>{{ $customRequest->title }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Tanggal Pembayaran:</span>
                                <span>{{ $customRequest->full_payment_date ? $customRequest->full_payment_date->format('d M Y, H:i') : 'Baru saja' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Metode Pembayaran:</span>
                                <span>Transfer Bank / Virtual Account</span>
                            </div>

                            <div class="receipt-row">
                                <span>Status Pembayaran:</span>
                                <span class="status-badge status-success">BERHASIL</span>
                            </div>

                            <div class="receipt-row">
                                <span>Jumlah Pelunasan:</span>
                                <strong>Rp
                                    {{ number_format($customRequest->remaining_payment + ($customRequest->shipment ? $customRequest->shipment->shipping_cost : 0), 0, ',', '.') }}</strong>
                            </div>

                            @if ($customRequest->shipment)
                                <div class="receipt-row">
                                    <span>Biaya Pengiriman:</span>
                                    <span>Rp
                                        {{ number_format($customRequest->shipment->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="receipt-row">
                                <span>Total Dibayar:</span>
                                <strong>Rp
                                    {{ number_format($customRequest->quoted_price + ($customRequest->shipment ? $customRequest->shipment->shipping_cost : 0), 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="next-steps">
                            <h5><i class="fa fa-truck"></i> Langkah Selanjutnya</h5>
                            <ul class="mb-0">
                                <li>Produk kustom Anda sudah siap dan akan segera diproses untuk pengiriman</li>
                                <li>Tim kami akan mengemas produk dengan hati-hati</li>
                                <li>Anda akan menerima nomor resi tracking dalam 1-2 hari kerja</li>
                                <li>Estimasi waktu pengiriman akan diberikan bersamaan dengan nomor resi</li>
                            </ul>
                        </div>

                        @if ($customRequest->shipment)
                            <div class="shipping-info">
                                <h5><i class="fa fa-map-marker"></i> Informasi Pengiriman</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Penerima:</strong><br>
                                            {{ $customRequest->shipment->full_name }}<br>
                                            {{ $customRequest->shipment->phone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Alamat:</strong><br>
                                            {{ $customRequest->shipment->address }}<br>
                                            {{ $customRequest->shipment->city->name ?? '' }},
                                            {{ $customRequest->shipment->province->name ?? '' }}<br>
                                            {{ $customRequest->shipment->postal_code }}</p>
                                    </div>
                                </div>

                                @if ($customRequest->shipment->notes)
                                    <p><strong>Catatan:</strong> {{ $customRequest->shipment->notes }}</p>
                                @endif

                                <div class="d-flex align-items-center mt-3">
                                    <span class="status-badge status-ready">SIAP DIKIRIM</span>
                                    <span class="ml-3">Produk akan segera diproses untuk pengiriman</span>
                                </div>
                            </div>
                        @endif

                        <div class="btn-group">
                            <a href="{{ route('custom.show', $customRequest->id) }}" class="btn amado-btn">
                                <i class="fa fa-eye"></i> Lihat Detail Permintaan
                            </a>
                            <a href="{{ route('custom.my-requests') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-list"></i> Lihat Semua Permintaan
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fa fa-home"></i> Kembali ke Beranda
                            </a>
                        </div>

                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                Jika Anda memiliki pertanyaan tentang pengiriman, silakan hubungi customer service kami atau
                                <a href="{{ route('custom.show', $customRequest->id) }}">pantau status pengiriman</a>
                                di halaman detail permintaan.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="cart-summary">
                        <h5>Ringkasan Pembayaran</h5>
                        <ul class="summary-table">
                            <li><span>Harga Produk:</span>
                                <span>Rp {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</span>
                            </li>
                            @if ($customRequest->shipment)
                                <li><span>Biaya Pengiriman:</span>
                                    <span>Rp
                                        {{ number_format($customRequest->shipment->shipping_cost, 0, ',', '.') }}</span>
                                </li>
                            @endif
                            <li><span>DP Terbayar:</span>
                                <span class="text-success">Rp
                                    {{ number_format($customRequest->down_payment, 0, ',', '.') }}</span>
                            </li>
                            <li><span>Pelunasan:</span>
                                <span class="text-success">Rp
                                    {{ number_format($customRequest->remaining_payment + ($customRequest->shipment ? $customRequest->shipment->shipping_cost : 0), 0, ',', '.') }}</span>
                            </li>
                            <li class="font-weight-bold"><span>Total Terbayar:</span>
                                <span class="text-success">Rp
                                    {{ number_format($customRequest->quoted_price + ($customRequest->shipment ? $customRequest->shipment->shipping_cost : 0), 0, ',', '.') }}</span>
                            </li>
                        </ul>

                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="mb-2">
                                <i class="fa fa-calendar"></i> Status Pesanan
                            </h6>
                            <small class="text-muted">
                                <div class="mb-1">✅ Pembayaran DP - <strong>Selesai</strong></div>
                                <div class="mb-1">✅ Pengerjaan Produk - <strong>Selesai</strong></div>
                                <div class="mb-1">✅ Pembayaran Pelunasan - <strong>Selesai</strong></div>
                                <div class="mb-1">🚚 Persiapan Pengiriman - <strong>Sedang Berjalan</strong></div>
                                <div>📦 Pengiriman - <strong>Menunggu</strong></div>
                            </small>
                        </div>

                        {{-- Additional Invoice Button in Sidebar --}}
                        <div class="mt-4 text-center">
                            <a href="{{ route('invoice.custom.full', $customRequest->id) }}"
                                class="btn btn-invoice btn-sm w-100" target="_blank">
                                <i class="fa fa-file-pdf-o"></i> Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
