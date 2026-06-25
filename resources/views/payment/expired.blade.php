{{-- resources/views/payment/expired.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>Pembayaran Kadaluarsa</h1>
        <p>Pembayaran untuk pesanan #{{ $order->id }} telah kadaluarsa. Silakan lakukan pemesanan ulang atau coba bayar
            kembali jika tersedia.</p>
        <a href="{{ route('account.orders.detail', $order->id) }}" class="btn btn-primary">Lihat Detail Pesanan</a>
    </div>
@endsection
