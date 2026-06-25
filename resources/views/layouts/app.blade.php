<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title  -->
    <title>Azwaely - Mebel</title>
    @yield('styles')

    <!-- Favicon  -->
    <link rel="icon" href="{{ asset('arman') }}/img/core-img/favicon.ico">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS - HANYA SATU KALI -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FancyBox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />

    <!-- Core Style CSS -->
    <link rel="stylesheet" href="{{ asset('arman') }}/css/core-style.css">
    <link rel="stylesheet" href="{{ asset('arman') }}/style.css">
    <link rel="stylesheet" href="{{ asset('arman') }}/css/modals.css">
    <!-- Auth Modal Styles (custom) -->

</head>

<body>
    @include('layouts.partials.bagianapp')

    <!-- Auth Modals -->
    @include('layouts.partials.auth-modals')



    <!-- ##### Footer Area End ##### -->

    <!-- LOADING ORDER YANG BENAR -->
    <!-- 1. jQuery dulu -->
    <script src="{{ asset('arman') }}/js/jquery/jquery-2.2.4.min.js"></script>

    <!-- 2. Bootstrap 5 JS Bundle - HANYA SATU KALI -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 3. Other plugins (SKIP bootstrap lama untuk avoid konflik) -->
    <script src="{{ asset('arman') }}/js/plugins.js"></script>
    <script src="{{ asset('arman') }}/js/active.js"></script>
    <script src="{{ asset('arman') }}/js/dropdown.js"></script>
    <!-- 4. FancyBox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    @yield('script')
    @stack('scripts')
</body>

</html>
