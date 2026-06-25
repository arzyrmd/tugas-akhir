@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-green-600 text-start text-sm font-medium text-green-700 bg-green-50 focus:outline-none focus:text-green-800 focus:bg-green-100 focus:border-green-700 transition-colors duration-200'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-sm font-medium text-gray-600 hover:text-green-600 hover:bg-green-50 hover:border-green-300 focus:outline-none focus:text-green-700 focus:bg-green-50 focus:border-green-400 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
