<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=poppins:600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="font-sans text-gray-900 antialiased relative overflow-auto">

    <div class="min-h-screen flex flex-col sm:justify-center items-center p-6 sm:p-0 bg-white shadow-md rounded-lg">

        <!-- Animated Decorative Elements -->
        <div class="absolute top-10 left-10 w-48 h-8 bg-green-500 opacity-20 rounded-lg animate-[slide_1.5s_ease-out]">
        </div>
        <div
            class="absolute bottom-10 right-10 w-64 h-10 bg-green-500 opacity-20 rounded-lg animate-[slide_1.5s_ease-out]">
        </div>
        <div class="absolute top-24 right-20 w-28 h-8 bg-blue-400 opacity-25 rounded-full animate-[slide_2s_ease-out]">
        </div>
        <div
            class="absolute bottom-20 left-20 w-16 h-16 bg-red-400 rounded-lg opacity-20 animate-[slide_1.8s_ease-out]">
        </div>
        <div class="absolute top-32 right-24 w-20 h-8 bg-yellow-400 opacity-25 rounded-lg animate-[slide_2s_ease-out]">
        </div>
        <div
            class="absolute bottom-32 right-12 w-14 h-14 bg-purple-500 rounded-xl opacity-30 animate-[slide_1.5s_ease-out]">
        </div>

        <!-- Logo Section -->
        <div class="w-full sm:max-w-md flex flex-col items-center mb-6">
            <div class="w-12 h-1.5 bg-green-500 rounded-full mt-4 animate-pulse"></div>
        </div>

        <!-- Content Card -->
        <div
            class="w-full sm:max-w-md px-8 py-8 bg-white shadow-lg rounded-2xl border border-gray-200 relative overflow-hidden">
            <!-- Decorative top border -->
            <div
                class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-400 to-green-600 animate-[pulse_2s_infinite]">
            </div>
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="w-full sm:max-w-md mt-6 text-center text-sm text-gray-500">
            <div class="flex justify-center space-x-4 mb-2">
                <a href="{{ route('home') }}"
                    class="hover:text-green-600 transition-transform transform hover:scale-105 duration-200">Home</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('products.index') }}"
                    class="hover:text-green-600 transition-transform transform hover:scale-105 duration-200">Products</a>
                <span class="text-gray-300">|</span>
                <a href="#"
                    class="hover:text-green-600 transition-transform transform hover:scale-105 duration-200">Contact</a>
            </div>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
