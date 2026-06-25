@extends('layouts.app')
@section('content')
    @include('notifikasi.notifikasi')
    <!-- Product Catagories Area Start -->
    <div class="products-catagories-area clearfix">
        <div class="amado-pro-catagory clearfix">
            @foreach ($categories as $category)
                <!-- Single Catagory -->
                <div class="single-products-catagory clearfix">
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}">
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                        <!-- Hover Content -->
                        <div class="hover-content">
                            <div class="line"></div>
                            @if ($category->cheapestProduct)
                                <p>Mulai dari Rp {{ number_format($category->cheapestProduct->price, 0, ',', '.') }}</p>
                            @endif
                            <h4>{{ $category->name }}</h4>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Product Catagories Area End -->
@endsection
