@extends('layouts.app')

@section('content')
    <div class="shop_sidebar_area">

        <!-- ##### Single Widget ##### -->
        <div class="widget catagory mb-50">
            <!-- Widget Title -->
            <h6 class="widget-title mb-30">Catagories</h6>

            <!--  Catagories  -->
            <div class="catagories-menu">
                <ul>
                    @foreach ($categories as $category)
                        <li class="{{ isset($selectedCategory) && $selectedCategory->id == $category->id ? 'active' : '' }}">
                            <a
                                href="{{ route('products.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="amado_product_area section-padding-100">
        <div class="container-fluid">

            <!-- Include notifikasi component -->
            @include('notifikasi.notifikasi')

            <div class="row">
                <div class="col-12">
                    <div class="product-topbar d-xl-flex align-items-end justify-content-between">
                        <!-- Total Products -->
                        <div class="total-products">
                            @if (isset($keyword))
                                <p>Hasil pencarian untuk: "{{ $keyword }}"</p>
                            @endif
                            <p>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of
                                {{ $products->total() }}</p>
                            <div class="view d-flex">
                                <a href="#"><i class="fa fa-th-large" aria-hidden="true"></i></a>
                                <a href="#"><i class="fa fa-bars" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <!-- Sorting -->
                        <div class="product-sorting d-flex">
                            <div class="sort-by-date d-flex align-items-center mr-15">
                                <p>Sort by</p>
                                <form action="{{ isset($keyword) ? route('products.search') : route('products.index') }}"
                                    method="get" id="sort-form">
                                    @if (isset($keyword))
                                        <input type="hidden" name="keyword" value="{{ $keyword }}">
                                    @endif
                                    @if (isset($selectedCategory))
                                        <input type="hidden" name="category" value="{{ $selectedCategory->slug }}">
                                    @endif
                                    @if (isset($minPrice))
                                        <input type="hidden" name="min_price" value="{{ $minPrice }}">
                                    @endif
                                    @if (isset($maxPrice))
                                        <input type="hidden" name="max_price" value="{{ $maxPrice }}">
                                    @endif
                                    @if (isset($perPage))
                                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                                    @endif
                                    <select name="sort" id="sortBydate"
                                        onchange="document.getElementById('sort-form').submit()">
                                        <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
                                        <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price: Low
                                            to High</option>
                                        <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price:
                                            High to Low</option>
                                        <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Popular
                                        </option>
                                    </select>
                                </form>
                            </div>
                            <div class="view-product d-flex align-items-center">
                                <p>View</p>
                                <form action="{{ isset($keyword) ? route('products.search') : route('products.index') }}"
                                    method="get" id="per-page-form">
                                    @if (isset($keyword))
                                        <input type="hidden" name="keyword" value="{{ $keyword }}">
                                    @endif
                                    @if (isset($selectedCategory))
                                        <input type="hidden" name="category" value="{{ $selectedCategory->slug }}">
                                    @endif
                                    @if (isset($minPrice))
                                        <input type="hidden" name="min_price" value="{{ $minPrice }}">
                                    @endif
                                    @if (isset($maxPrice))
                                        <input type="hidden" name="max_price" value="{{ $maxPrice }}">
                                    @endif
                                    @if (isset($sort))
                                        <input type="hidden" name="sort" value="{{ $sort }}">
                                    @endif
                                    <select name="per_page" id="viewProduct"
                                        onchange="document.getElementById('per-page-form').submit()">
                                        <option value="4" {{ $perPage == 4 ? 'selected' : '' }}>4</option>
                                        <option value="8" {{ $perPage == 8 ? 'selected' : '' }}>8</option>
                                        <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>12</option>
                                        <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>24</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse($products as $product)
                    <!-- Single Product Area -->
                    <div class="col-12 col-sm-6 col-md-12 col-xl-6">
                        <div class="single-product-wrapper">
                            <!-- Product Image -->
                            <div class="product-img">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                <!-- Hover Thumb -->
                                @if ($product->gallery && count($product->gallery) > 0)
                                    <img class="hover-img" src="{{ asset('storage/' . $product->gallery[0]) }}"
                                        alt="{{ $product->name }}">
                                @else
                                    <img class="hover-img" src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}">
                                @endif
                            </div>

                            <!-- Product Description -->
                            <div class="product-description d-flex align-items-center justify-content-between">
                                <!-- Product Meta Data -->
                                <div class="product-meta-data">
                                    <div class="line"></div>
                                    <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <h6>{{ $product->name }}</h6>
                                    </a>
                                </div>
                                <!-- Ratings & Cart -->
                                <div class="ratings-cart text-right">
                                    <div class="ratings">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <div class="cart text-center">
                                        @if ($product->stock > 0)
                                            <button type="button" class="add-to-cart-btn"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}" data-toggle="tooltip"
                                                data-placement="left" title="Add to Cart"
                                                style="background: none; border: none; padding: 0">
                                                <img src="{{ asset('arman') }}/img/core-img/cart.png" alt="">
                                            </button>
                                        @else
                                            <button type="button" disabled
                                                style="background: none; border: none; padding: 0; opacity: 0.5; cursor: not-allowed"
                                                title="Stok Habis">
                                                <img src="{{ asset('arman') }}/img/core-img/cart.png" alt="">
                                            </button>
                                            <p class="text-danger mt-1" style="font-size: 12px;">Stok Habis</p>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Tidak ada produk yang ditemukan. Coba filter atau kategori lain.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Pagination -->
                    <nav aria-label="navigation">
                        <ul class="pagination justify-content-end mt-50">
                            {{ $products->appends(request()->query())->links('pagination.custom') }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .add-to-cart-btn {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-to-cart-btn img {
            transition: transform 0.2s ease;
        }

        .add-to-cart-btn:hover img {
            transform: scale(1.1);
        }

        .add-to-cart-btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: calc(50% - 8px);
            left: calc(50% - 8px);
            border: 2px solid rgba(251, 183, 16, 0.3);
            border-radius: 50%;
            border-top-color: #e3a302;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Efek pop-up untuk notifikasi tambah ke keranjang */
        .product-added-effect {
            animation: productAdded 0.4s ease-out;
        }

        @keyframes productAdded {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF token untuk Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Event listener untuk tombol add to cart
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');

                    // Tampilkan loading state
                    this.classList.add('loading');
                    this.style.opacity = "0.7";

                    // Buat form data
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('quantity', 1);

                    fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Reset tampilan tombol
                            this.classList.remove('loading');
                            this.style.opacity = "1";

                            // Tampilkan notifikasi
                            if (window.showNotification) {
                                window.showNotification(data.type, data.message);
                            }

                            // Update jumlah item di keranjang (jika ada)
                            if (data.cart_count !== undefined) {
                                // Update cart count di menu nav
                                const cartCountElements = document.querySelectorAll(
                                    '.cart-count');
                                cartCountElements.forEach(element => {
                                    // Pastikan hanya mengganti angka dalam tanda kurung
                                    element.textContent = '(' + data.cart_count + ')';
                                });
                            }

                            // Animasi sukses
                            const img = this.querySelector('img');
                            if (img) {
                                img.classList.add('product-added-effect');
                                setTimeout(() => {
                                    img.classList.remove('product-added-effect');
                                }, 500);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            // Reset tampilan tombol
                            this.classList.remove('loading');
                            this.style.opacity = "1";

                            // Tampilkan notifikasi error
                            if (window.showNotification) {
                                window.showNotification('error',
                                    'Gagal menambahkan produk ke keranjang.');
                            }
                        });
                });
            });

            // Inisialisasi tooltip jika Bootstrap tooltip digunakan
            if (typeof $(document).tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    </script>
@endsection
