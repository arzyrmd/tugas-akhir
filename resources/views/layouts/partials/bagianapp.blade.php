<!-- Search Wrapper Area Start -->
<div class="search-wrapper section-padding-100">
    <div class="search-close">
        <i class="fa fa-close" aria-hidden="true"></i>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="search-content">
                    <form action="{{ route('products.search') }}" method="get">
                        <input type="search" name="keyword" id="search" placeholder="Type your keyword...">
                        <button type="submit"><img src="{{ asset('arman') }}/img/core-img/search.png"
                                alt=""></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Search Wrapper Area End -->
<!-- Search Wrapper Area End -->

<!-- ##### Main Content Wrapper Start ##### -->
<div class="main-content-wrapper d-flex clearfix">

    <!-- Mobile Nav (max width 767px)-->
    <div class="mobile-nav">
        <!-- Navbar Brand -->
        <div class="amado-navbar-brand">
            <a href="index.html"><img src="{{ asset('arman') }}/img/core-img/logo.png" alt=""></a>
        </div>
        <!-- Navbar Toggler -->
        <div class="amado-navbar-toggler">
            <span></span><span></span><span></span>
        </div>
    </div>
    @include('layouts.navigation.navigasi')
    @yield('content')
    <!-- ##### Main Content Wrapper End ##### -->
</div>

<section class="custom-request-area section-padding-100-0">
    <div class="container">
        <div class="row align-items-center">
            <!-- Custom Request Text -->
            <div class="col-12 col-lg-6 col-xl-7">
                <div class="custom-request-text mb-100">
                    <h2>Buat <span>Permintaan Khusus</span> Anda</h2>
                    <p>Kami siap memenuhi kebutuhan khusus Anda. Ceritakan kepada kami apa yang Anda inginkan dan tim
                        kami akan berusaha mewujudkannya dengan kualitas terbaik dan harga yang bersaing.</p>
                </div>
            </div>
            <!-- Custom Request Button -->
            <div class="col-12 col-lg-6 col-xl-5">
                <div class="custom-request-form mb-100">
                    <a href="{{ route('custom.index') }}" class="custom-request-btn">Buat Permintaan Khusus</a>

                </div>
            </div>
        </div>
    </div>
</section>


<footer class="footer_area clearfix">
    <div class="container">
        <div class="row align-items-center">
            <!-- Single Widget Area -->

            <div class="col-12 col-lg-4">
                <div class="single_widget_area">
                    <!-- Logo -->




                    <!-- Google Maps -->
                    <div class="mt-3">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.238111064801!2d109.13259707504564!3d-6.893567268118081!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fb9b3f9c3b7d3%3A0x4ee7fb5d3a8b0657!2sAZWELY%20PUTRA!5e0!3m2!1sen!2sid!4v1716100000000!5m2!1sen!2sid"
                            width="100%" height="200" style="border:0; border-radius: 10px;" allowfullscreen=""
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                    <!-- Copywrite Text -->
                    <p class="copywrite">
                        Sistem Informasi Pemesanan Mebel x Program Studi D3 Teknik Komputer / Poltek Harber<br>
                        &copy;
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        SISFO x Program Studi D3 Teknik Komputer / Poltek Harber.
                    </p>
                </div>
            </div>

            <!-- Single Widget Area -->
            <div class="col-12 col-lg-8">
                <div class="single_widget_area">
                    <!-- Footer Menu -->
                    <div class="footer_menu">
                        <nav class="navbar navbar-expand-lg justify-content-end">
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#footerNavContent" aria-controls="footerNavContent" aria-expanded="false"
                                aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
                            <div class="collapse navbar-collapse" id="footerNavContent">
                                <ul class="navbar-nav ml-auto">
                                    <li class="nav-item active">
                                        <a class="nav-link" href="index.html">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="shop.html">Shop</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="product-details.html">Product</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="cart.html">Cart</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="checkout.html">Checkout</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
