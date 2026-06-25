{{-- resources/views/custom/payment-dp-success.blade.php --}}
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

        .status-processing {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .next-steps {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin-bottom: 20px;
        }

        .progress-info {
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

        .invoice-actions {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-print-invoice {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-print-invoice:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: white;
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
                            <h2>Pembayaran DP Berhasil!</h2>
                            <p class="lead">Terima kasih! Pembayaran DP untuk produk kustom Anda telah berhasil diproses.
                            </p>
                        </div>

                        <div class="invoice-actions">
                            <h5 class="mb-3">
                                <i class="fa fa-file-pdf-o"></i> Invoice & Bukti Pembayaran
                            </h5>
                            <p class="mb-3">Unduh invoice resmi untuk pembayaran DP Anda sebagai bukti transaksi.</p>
                            <div class="btn-group">
                                <a href="{{ route('invoice.custom.dp', $customRequest->id) }}" class="btn btn-print-invoice"
                                    target="_blank">
                                    <i class="fa fa-download"></i> Cetak Invoice DP
                                </a>

                            </div>
                        </div>

                        <div class="payment-receipt">
                            <h4 class="mb-4">
                                <i class="fa fa-receipt"></i> Bukti Pembayaran
                            </h4>

                            <div class="receipt-row">
                                <span>Nomor Transaksi:</span>
                                <strong>{{ $customRequest->dp_payment_code }}</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Produk:</span>
                                <span>{{ $customRequest->title }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Tanggal Pembayaran:</span>
                                <span>{{ $customRequest->dp_payment_date ? $customRequest->dp_payment_date->format('d M Y, H:i') : 'Baru saja' }}</span>
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
                                <span>Jumlah DP:</span>
                                <strong>Rp {{ number_format($customRequest->down_payment, 0, ',', '.') }}</strong>
                            </div>

                            <div class="receipt-row">
                                <span>Sisa Pembayaran:</span>
                                <strong>Rp {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="next-steps">
                            <h5><i class="fa fa-info-circle"></i> Langkah Selanjutnya</h5>
                            <ul class="mb-0">
                                <li>Tim kami akan segera memulai proses pengerjaan produk kustom Anda</li>
                                <li>Anda akan menerima update progress secara berkala</li>
                                <li>Estimasi waktu penyelesaian:
                                    <strong>{{ \Carbon\Carbon::parse($customRequest->estimated_completion)->format('d M Y') }}</strong>
                                </li>
                                <li>Pembayaran pelunasan akan dilakukan setelah produk selesai dan siap dikirim</li>
                            </ul>
                        </div>

                        <div class="progress-info">
                            <h5>Status Saat Ini</h5>
                            <div class="d-flex align-items-center mb-2">
                                <span
                                    class="status-badge status-processing">{{ $customRequest->status_label ?? 'DALAM PENGERJAAN' }}</span>
                                <span class="ml-3">Produk sedang dalam proses pengerjaan</span>
                            </div>

                            @if ($customRequest->estimated_completion)
                                <p class="mb-0">
                                    <strong>Estimasi Selesai:</strong>
                                    {{ \Carbon\Carbon::parse($customRequest->estimated_completion)->format('d M Y') }}
                                    ({{ \Carbon\Carbon::parse($customRequest->estimated_completion)->diffForHumans() }})
                                </p>
                            @endif
                        </div>

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
                                Jika Anda memiliki pertanyaan, silakan hubungi customer service kami atau
                                <a href="{{ route('custom.show', $customRequest->id) }}">pantau progress pengerjaan</a>
                                di halaman detail permintaan.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="cart-summary">
                        <h5>Ringkasan Pembayaran</h5>
                        <ul class="summary-table">
                            <li><span>Total Harga:</span>
                                <span>Rp {{ number_format($customRequest->quoted_price, 0, ',', '.') }}</span>
                            </li>
                            <li><span>DP Dibayar:</span>
                                <span class="text-success">Rp
                                    {{ number_format($customRequest->down_payment, 0, ',', '.') }}</span>
                            </li>
                            <li><span>Sisa Pembayaran:</span>
                                <span>Rp {{ number_format($customRequest->remaining_payment, 0, ',', '.') }}</span>
                            </li>
                        </ul>

                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="mb-2">
                                <i class="fa fa-calendar"></i> Timeline Pengerjaan
                            </h6>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
