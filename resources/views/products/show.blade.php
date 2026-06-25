@extends('layouts.app')

@section('content')
    <!-- Product Details Area Start -->
    <div class="single-product-area section-padding-100 clearfix">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mt-50">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('products.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-7">
                    <div class="single_product_thumb">
                        <div id="product_details_slider" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li class="active" data-target="#product_details_slider" data-slide-to="0"
                                    style="background-image: url({{ asset('storage/' . $product->image) }});">
                                </li>
                                @if ($product->gallery && is_array($product->gallery))
                                    @foreach ($product->gallery as $index => $image)
                                        <li data-target="#product_details_slider" data-slide-to="{{ $index + 1 }}"
                                            style="background-image: url({{ asset('storage/' . $image) }});">
                                        </li>
                                    @endforeach
                                @endif
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <a class="gallery_img" href="{{ asset('storage/' . $product->image) }}">
                                        <img class="d-block w-100" src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}">
                                    </a>
                                </div>
                                @if ($product->gallery && is_array($product->gallery))
                                    @foreach ($product->gallery as $image)
                                        <div class="carousel-item">
                                            <a class="gallery_img" href="{{ asset('storage/' . $image) }}">
                                                <img class="d-block w-100" src="{{ asset('storage/' . $image) }}"
                                                    alt="{{ $product->name }}">
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="single_product_desc">
                        <!-- Product Meta Data -->
                        <div class="product-meta-data">
                            <div class="line"></div>
                            <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <a href="#">
                                <h6>{{ $product->name }}</h6>
                            </a>
                            <!-- Ratings & Review -->
                            <div class="ratings-review mb-15 d-flex align-items-center justify-content-between">
                                <div class="ratings">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </div>
                                <div class="review">
                                    <a href="#">Write A Review</a>
                                </div>
                            </div>
                            <!-- Avaiable -->
                            <p class="avaibility">
                                <i class="fa fa-circle" style="color: {{ $product->stock > 0 ? 'green' : 'red' }}"></i>
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </p>
                        </div>

                        <div class="short_overview my-5">
                            <p>{{ $product->description }}</p>
                        </div>

                        <!-- Add to Cart Form -->
                        <form class="cart clearfix" method="post" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="cart-btn d-flex mb-50">
                                <p>Jumlah</p>
                                <div class="quantity">
                                    <span class="qty-minus"
                                        onclick="var effect = document.getElementById('qty'); var qty = effect.value; if( !isNaN( qty ) &amp;&amp; qty &gt; 1 ) effect.value--;return false;">
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </span>
                                    <input type="number" class="qty-text" id="qty" step="1" min="1"
                                        max="{{ $product->stock }}" name="quantity" value="1">
                                    <span class="qty-plus"
                                        onclick="var effect = document.getElementById('qty'); var qty = effect.value; if( !isNaN( qty )) effect.value++;return false;">
                                        <i class="fa fa-caret-up" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" name="addtocart" value="5" class="btn amado-btn"
                                {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                Tambah keranjang
                            </button>
                        </form>

                        <!-- Detail produk tambahan -->
                        <div class="product-details mt-5">
                            <h6>Detail Produk</h6>
                            <ul class="mt-3">

                                @if ($product->length)
                                    <li><strong>Dimensi:</strong> {{ $product->length }} x {{ $product->width }} x
                                        {{ $product->height }} cm</li>
                                @endif
                                @if ($product->material)
                                    <li><strong>Material:</strong> {{ $product->material }}</li>
                                @endif
                                <li><strong>Kategori:</strong> <a
                                        href="{{ route('products.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Details Area End -->
@endsection

@push('scripts')
    <script>
        // Script untuk gallery
        $(document).ready(function() {
            $('.gallery_img').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        });
    </script>
@endpush
