<!-- resources/views/account/orders.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="cart-table-area section-padding-100 clearfix">
        <div class="container-fluid">
            <div class="row mt-30">
                <div class="col-12">
                    <!-- Custom Notification System -->
                    <div id="notification-container"
                        style="position: fixed; top: 20px; left: 20px; max-width: 350px; z-index: 9999;"></div>

                    <div class="orders-wrapper">
                        <div class="orders-filter mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2>Pesanan Saya</h2>
                                    @if (!$orders->isEmpty())
                                        <p class="mb-0" style="font-size: 14px; color: #6c757d;">
                                            <strong>{{ $orders->total() }}</strong> pesanan ditemukan
                                        </p>
                                    @endif
                                </div>
                                @if (!$orders->isEmpty())
                                    <div class="col-md-6 text-md-right">
                                        <div class="d-flex justify-content-md-end">
                                            <a href="{{ route('products.index') }}" class="btn mr-2"
                                                style="background-color: #fbb710; color: #fff; border-radius: 4px; padding: 6px 12px; font-size: 13px;">
                                                <i class="fa fa-plus-circle mr-1"></i> Tambah
                                            </a>

                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($orders->isEmpty())
                            <div class="empty-orders text-center py-5"
                                style="background-color: #f9f9f9; border-radius: 5px; padding: 50px 20px;">
                                <i class="fa fa-shopping-bag fa-4x mb-4" style="color: #d6d6d6;"></i>
                                <h4 style="color: #6c757d; margin-bottom: 15px;">Anda belum memiliki pesanan</h4>
                                <p style="color: #6c757d; margin-bottom: 25px;">Jelajahi produk kami dan mulai belanja
                                    sekarang</p>
                                <a href="{{ route('products.index') }}" class="btn"
                                    style="background-color: #fbb710; color: #fff; border-radius: 4px; padding: 8px 15px; font-size: 14px;">
                                    <i class="fa fa-shopping-cart mr-1"></i> Mulai Belanja
                                </a>
                            </div>
                        @else
                            <!-- Orders List with Cards -->
                            <div class="orders-list">
                                @foreach ($orders as $order)
                                    @php
                                        $statusColors = [
                                            'MENUNGGU PEMBAYARAN' => ['#ffc107', '#212529'],
                                            'PEMBAYARAN BERHASIL' => ['#17a2b8', '#fff'],
                                            'DIKEMAS' => ['#007bff', '#fff'],
                                            'SIAP DIKIRIM' => ['#6610f2', '#fff'],
                                            'DIKIRIM' => ['#6c757d', '#fff'],
                                            'SELESAI' => ['#28a745', '#fff'],
                                            'DIBATALKAN' => ['#dc3545', '#fff'],
                                        ];
                                        [$bgColor, $textColor] = $statusColors[$order->status] ?? ['#6c757d', '#fff'];
                                    @endphp

                                    <div class="order-card mb-4 border rounded" style="overflow: hidden;">
                                        <div
                                            class="order-header d-flex justify-content-between align-items-center bg-light px-4 py-3 border-bottom">
                                            <h5 class="mb-0" style="font-size: 16px; font-weight: 600;">
                                                Pesanan #{{ $order->payment_code }}
                                            </h5>
                                            <span class="badge"
                                                style="
                    background-color: {{ $bgColor }};
                    color: {{ $textColor }};
                    font-size: 12px;
                    padding: 5px 8px;
                    font-weight: 500;
                    border-radius: 3px;">
                                                {{ $order->status }}
                                            </span>
                                        </div>

                                        <div class="order-body px-4 py-3">
                                            <div class="row">
                                                <div class="col-md-3 mb-3 mb-md-0">
                                                    <p class="text-muted mb-1" style="font-size: 12px;">Tanggal Pesanan</p>
                                                    <p class="mb-0" style="font-size: 14px;">
                                                        <i class="fa fa-calendar-o mr-1 text-warning"></i>
                                                        {{ $order->order_created_at->format('d M Y') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-3 mb-3 mb-md-0">
                                                    <p class="text-muted mb-1" style="font-size: 12px;">Waktu Pemesanan</p>
                                                    <p class="mb-0" style="font-size: 14px;">
                                                        <i class="fa fa-clock-o mr-1 text-warning"></i>
                                                        {{ $order->order_created_at->format('H:i') }} WIB
                                                    </p>
                                                </div>

                                                <div class="col-md-3 mb-3 mb-md-0">
                                                    <p class="text-muted mb-1" style="font-size: 12px;">Total Pembayaran</p>
                                                    <p class="mb-0"
                                                        style="font-size: 14px; font-weight: 600; color: #fbb710;">
                                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                                    </p>
                                                </div>

                                                <div class="col-md-3 text-md-right">
                                                    <div class="d-flex justify-content-md-end flex-wrap">
                                                        <a href="{{ route('account.orders.detail', $order) }}"
                                                            class="btn btn-sm mr-2 mb-1"
                                                            style="background-color: #fafafa; color: #333; border: 1px solid #ddd; font-size: 12px; border-radius: 3px;">
                                                            <i class="fa fa-eye mr-1"></i> Detail
                                                        </a>

                                                        @if ($order->status === 'PEMBAYARAN BERHASIL')
                                                            <a href="{{ route('invoice.print', $order->id) }}"
                                                                class="btn btn-sm mr-2 mb-1"
                                                                style="background-color: #fafafa; color: #333; border: 1px solid #ddd; font-size: 12px; border-radius: 3px;">
                                                                <i class="fa fa-eye mr-1"></i> Cetak Invoice
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>


                            <!-- Pagination yang diperbaiki dan terpusat -->
                            <div class="pagination-wrapper mt-4 d-flex justify-content-center">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination" style="margin-bottom: 0;">
                                        {{-- Previous Page Link --}}
                                        @if ($orders->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"
                                                    style="background-color: #f8f9fa; color: #6c757d; border-color: #dee2e6; font-size: 14px; padding: 6px 12px; border-radius: 3px;">&laquo;</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $orders->previousPageUrl() }}"
                                                    style="background-color: #fff; color: #fbb710; border-color: #dee2e6; font-size: 14px; padding: 6px 12px; border-radius: 3px;">&laquo;</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @for ($i = 1; $i <= $orders->lastPage(); $i++)
                                            <li class="page-item {{ $orders->currentPage() == $i ? 'active' : '' }}"
                                                style="margin: 0 3px;">
                                                <a class="page-link" href="{{ $orders->url($i) }}"
                                                    style="{{ $orders->currentPage() == $i ? 'background-color: #fbb710; border-color: #fbb710; color: #fff;' : 'background-color: #fff; color: #fbb710; border-color: #dee2e6;' }} font-size: 14px; padding: 6px 12px; min-width: 36px; text-align: center; border-radius: 3px;">
                                                    {{ $i }}
                                                </a>
                                            </li>
                                        @endfor

                                        {{-- Next Page Link --}}
                                        @if ($orders->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $orders->nextPageUrl() }}"
                                                    style="background-color: #fff; color: #fbb710; border-color: #dee2e6; font-size: 14px; padding: 6px 12px; border-radius: 3px;">&raquo;</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"
                                                    style="background-color: #f8f9fa; color: #6c757d; border-color: #dee2e6; font-size: 14px; padding: 6px 12px; border-radius: 3px;">&raquo;</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</div @endsection
