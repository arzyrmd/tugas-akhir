@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <li class="page-item disabled"><span class="page-link">01.</span></li>
    @else
        <li class="page-item"><a class="page-link"
                href="{{ $paginator->previousPageUrl() }}">{{ sprintf('%02d', $paginator->currentPage() - 1) }}.</a></li>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link">{{ sprintf('%02d', $page) }}.</span></li>
                @else
                    <li class="page-item"><a class="page-link"
                            href="{{ $url }}">{{ sprintf('%02d', $page) }}.</a></li>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <li class="page-item"><a class="page-link"
                href="{{ $paginator->nextPageUrl() }}">{{ sprintf('%02d', $paginator->currentPage() + 1) }}.</a></li>
    @else
        <li class="page-item disabled"><span class="page-link">{{ sprintf('%02d', $paginator->lastPage()) }}.</span>
        </li>
    @endif
@endif
