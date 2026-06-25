{{-- resources/views/payment/cancelled.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="text-center">
        <h1>Pembayaran Dibatalkan</h1>
        <p>Pesanan #{{ $order->id }} telah dibatalkan. Jika ini tidak disengaja, Anda bisa mencoba lagi.</p>
        <a href="{{ route('account.orders.detail', $order->id) }}" class="btn btn-primary">Lihat Detail Pesanan</a>
    </div>
@endsection
