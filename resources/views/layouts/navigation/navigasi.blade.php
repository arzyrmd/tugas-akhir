<!-- Header Area Start -->
<header class="header-area clearfix">
    <!-- Close Icon -->
    <div class="nav-close">
        <i class="fa fa-close" aria-hidden="true"></i>
    </div>

    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset('arman') }}/img/core-img/name.png" alt="">
        </a>
    </div>

    <!-- Amado Nav -->
    <nav class="amado-nav">
        <ul>
            <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">Home</a>
            </li>
            <li class="{{ request()->routeIs('products.index') ? 'active' : '' }}">
                <a href="{{ route('products.index') }}">Shop</a>
            </li>
            <li class="{{ request()->routeIs('custom.index') || request()->routeIs('custom.create') ? 'active' : '' }}">
                <a href="{{ route('custom.index') }}">Custom Order</a>
            </li>
            <li class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">
                <a href="{{ route('cart.index') }}">Cart</a>
            </li>

        </ul>
    </nav>

    @guest
        <!-- Button Group (Login & Register) -->
        <div class="amado-btn-group mt-30 mb-100">
            <a href="#" class="btn amado-btn mb-15" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="fa fa-sign-in"></i> {{ __('Login') }}
            </a>
            <a href="#" class="btn amado-btn active" data-bs-toggle="modal" data-bs-target="#registerModal">
                <i class="fa fa-user-plus"></i> {{ __('Register') }}
            </a>
        </div>
    @endguest

    <!-- User Account & Login Links -->
    <div class="user-account-area mt-30 mb-20">
        @auth
            <div class="custom-dropdown">
                <button class="dropdown-btn" onclick="toggleDropdown()">
                    <i class="fa fa-user-circle"></i>
                    <span class="user-name">
                        {{ Str::of(Auth::user()->name)->explode(' ')->first() }}
                    </span>
                    <i class="fa fa-chevron-down dropdown-arrow"></i>
                </button>

                <div class="dropdown-content" id="userDropdown">
                    <a href="{{ route('profile.edit') }}" class="dropdown-link">
                        <i class="fa fa-user-circle"></i>
                        <span>{{ __('My Profile') }}</span>
                    </a>
                    <a href="{{ route('account.orders') }}" class="dropdown-link">
                        <i class="fa fa-shopping-bag"></i>
                        <span>{{ __('Pesanan Saya') }}</span>
                    </a>
                    <a href="{{ route('custom.my-requests') }}" class="dropdown-link">
                        <i class="fa fa-pencil-alt"></i>
                        <span>{{ __('Pesanan Request') }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                        @csrf
                        <button type="submit" class="dropdown-link logout-btn">
                            <i class="fa fa-sign-out-alt"></i>
                            <span>{{ __('Log Out') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
    <!-- Cart Menu -->
    <div class="cart-fav-search mb-100">

        <a href="{{ route('cart.index') }}" class="cart-nav">
            <img src="{{ asset('arman') }}/img/core-img/cart.png" alt="">
            Keranjang
            <span class="cart-count">
                @php
                    $cart = Auth::check()
                        ? \App\Models\Cart::where('user_id', Auth::id())->first()
                        : \App\Models\Cart::where('session_id', session()->getId())->first();

                    $itemCount = 0;
                    if ($cart) {
                        $itemCount = \App\Models\CartItem::where('cart_id', $cart->id)->sum('quantity');
                    }
                @endphp
                (<span class="text-amber-500 font-medium">{{ $itemCount }}</span>)
            </span>
        </a>

        <a href="#" class="search-nav">
            <img src="{{ asset('arman') }}/img/core-img/search.png" alt="">
            Search
        </a>

    </div>

    <!-- Social Button -->
    <div class="social-info d-flex justify-content-between">
        <a href="https://wa.me/085712424969" target="_blank" class="text-decoration-none text-dark me-3">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://facebook.com/namapage" target="_blank" class="text-decoration-none text-dark me-3">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://twitter.com/namapage" target="_blank" class="text-decoration-none text-dark me-3">
            <i class="fab fa-twitter"></i>
        </a>
        <a href="https://instagram.com/namapage" target="_blank" class="text-decoration-none text-dark">
            <i class="fab fa-instagram"></i>
        </a>
    </div>
</header>
