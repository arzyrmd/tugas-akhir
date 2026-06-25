<!-- resources/views/payment/success.blade.php -->
@extends('layouts.app')
@section('content')
    <div class="cart-table-area section-padding-10">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="checkout_details_area mt-50 clearfix">
                        <!-- Payment Success Header -->
                        <div class="text-center mb-5">
                            <i class="fa fa-check-circle fa-5x mb-4" style="color: #28a745;"></i>
                            <div class="cart-title">
                                <h2>Pembayaran Berhasil</h2>
                            </div>
                            <p style="font-size: 14px;">Terima kasih! Pembayaran Anda telah berhasil diproses.</p>
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
                                        <span style="font-size: 14px; color: #6d6d6d;">Via Bank:</span>
                                        <span style="font-size: 14px; font-weight: 500;">{{ $order->payment_method }}</span>
                                    </div>

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Total Pembayaran:</span>
                                        <span style="font-size: 14px; font-weight: 600; color: #fbb710;">Rp
                                            {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="info-item d-flex justify-content-between align-items-center mb-3">
                                        <span style="font-size: 14px; color: #6d6d6d;">Status:</span>
                                        <span class="badge"
                                            style="background-color: #28a745; color: #fff; font-weight: 400; font-size: 12px; padding: 5px 10px;">{{ $order->status }}</span>
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

                        <!-- Order Items Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="order-items-card">
                                    <h4 style="font-size: 18px; margin-bottom: 15px;">Detail Produk</h4>
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
                    </div>
                </div>

                <!-- SIDEBAR COLUMN -->
                <div class="col-12 col-lg-4">
                    <!-- Order Status Summary -->
                    <div class="cart-summary">
                        <h5>Status Pesanan</h5>
                        <div class="order-status mb-4">
                            <!-- Status Badge Centered - No Label -->
                            <div class="text-center mb-3">
                                <span class="badge"
                                    style="background-color: #28a745; color: #fff; font-weight: 500; font-size: 14px; padding: 8px 15px; display: inline-block; margin: 10px 0;">{{ $order->status }}</span>
                            </div>

                            <p style="font-size: 14px; text-align: center; margin-bottom: 20px;">Pembayaran Anda telah
                                diterima dan pesanan Anda sedang diproses.</p>
                        </div>

                        <!-- Action Buttons Centered With Improved Text Styling -->
                        <div class="mb-4">
                            <a href="{{ route('home') }}" class="amado-btn w-100 mb-3"
                                style="text-align: center; display: flex; justify-content: center; align-items: center; height: 45px; font-size: 15px; font-weight: 500;">
                                Kembali ke Beranda
                            </a>
                            <a href="{{ route('invoice.print', $order->id) }}" class="amado-btn w-100 mb-3"
                                style="text-align: center; display: flex; justify-content: center; align-items: center; height: 45px; font-size: 15px; font-weight: 500;">
                                Cetak Invoice
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
