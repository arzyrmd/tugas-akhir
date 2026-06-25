<!-- resources/views/payment/pending.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <!-- Payment Pending Header -->
                        <div class="text-center mb-5">
                            <i class="fa fa-clock-o fa-5x mb-4" style="color: #ffc107;"></i>
                            <div class="cart-title">
                                <h2>Pembayaran Tertunda</h2>
                            </div>
                            <p style="font-size: 14px;">Pembayaran Anda sedang diproses dan memerlukan tindakan lebih lanjut.
                            </p>
                        </div>

                        <!-- Order Info Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="order-info-card"
                                    style="border: 1px solid #e8e8e8; border-radius: 5px; padding: 20px; background-color: #fff;">
                                    <h4 style="font-size: 18px; margin-bottom: 20px; color: #242424;">Informasi Pesanan</h4>

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Nomor Pesanan:</span>
                                        <span style="font-size: 14px; font-weight: 500;">{{ $order->payment_code }}</span>
                                    </div>

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Total Pembayaran:</span>
                                        <span style="font-size: 14px; font-weight: 600; color: #fbb710;">Rp
                                            {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Status:</span>
                                        <span class="badge"
                                            style="background-color: #ffc107; color: #212529; font-weight: 400; font-size: 12px; padding: 5px 10px;">{{ $order->status }}</span>
                                    </div>

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Tanggal Pemesanan:</span>
                                        <span
                                            style="font-size: 14px; font-weight: 500;">{{ $order->order_created_at->format('d M Y H:i') }}</span>
                                    </div>

                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span style="font-size: 14px; color: #6d6d6d;">Tanggal Pembayaran:</span>
                                        <span
                                            style="font-size: 14px; font-weight: 500;">{{ $order->payment_date ? $order->payment_date->format('d M Y H:i') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="instruction-card"
                                    style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 5px; padding: 20px;">
                                    <h4 style="font-size: 18px; margin-bottom: 15px;">Tindakan Diperlukan</h4>
                                    <p style="font-size: 14px; margin-bottom: 8px;">Silakan selesaikan pembayaran Anda
                                        sesuai dengan instruksi yang telah diberikan melalui email atau halaman pembayaran.
                                    </p>
                                    <p style="font-size: 14px; margin-bottom: 8px;">Pesanan Anda akan diproses setelah
                                        pembayaran berhasil dikonfirmasi.</p>
                                    <p style="font-size: 14px;">Untuk melihat status pesanan, kunjungi halaman <a
                                            href="{{ route('account.orders') }}">Pesanan Saya</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR COLUMN -->
                <div class="col-12 col-lg-4">
                    <!-- Order Status Summary -->
                    <div class="cart-summary">
                        <h5>Status Pesanan</h5>
                        <div class="order-status mb-4">
                            <div class="text-center mb-3">
                                <span class="badge"
                                    style="background-color: #ffc107; color: #212529; font-weight: 500; font-size: 14px; padding: 8px 15px; display: inline-block; margin: 10px 0;">{{ $order->status }}</span>
                            </div>
                            <p style="font-size: 14px; text-align: center; margin-bottom: 20px;">Pembayaran Anda belum
                                dikonfirmasi. Mohon segera lakukan pembayaran agar pesanan dapat diproses.</p>
                        </div>

                        <div class="mb-4">
                            <a href="{{ route('home') }}" class="amado-btn w-100 mb-3"
                                style="text-align: center; display: flex; justify-content: center; align-items: center; height: 45px; font-size: 15px; font-weight: 500;">
                                Kembali ke Beranda
                            </a>
                            <a href="{{ route('account.orders') }}" class="amado-btn active w-100"
                                style="text-align: center; display: flex; justify-content: center; align-items: center; height: 45px; font-size: 15px; font-weight: 500;">
                                Lihat Pesanan Saya
                            </a>
                        </div>
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
                                <i class="fa fa-phone mr-2" style="color: #fbb710; width: 20px;"></i>
                                <span style="font-size: 14px;">+62 123 4567 890</span>
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
@endsection
