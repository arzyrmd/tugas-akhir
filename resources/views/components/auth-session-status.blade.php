@props(['status'])

@if ($status)
    <div
        {{ $attributes->merge(['class' => 'flex items-center p-3 bg-green-50 border-l-4 border-green-500 rounded-r-lg']) }}>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-sm font-medium text-green-700">
            {{ $status }}
        </span>
    </div>
@endif
