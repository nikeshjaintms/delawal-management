@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="pagination-nav-container">
        <div class="pagination-info-text">
            <p>
                {!! __('Showing') !!}
                @if ($paginator->firstItem())
                    <strong>{{ $paginator->firstItem() }}</strong>
                    {!! __('to') !!}
                    <strong>{{ $paginator->lastItem() }}</strong>
                @else
                    <strong>{{ $paginator->count() }}</strong>
                @endif
                {!! __('of') !!}
                <strong>{{ $paginator->total() }}</strong>
                {!! __('results') !!}
            </p>
        </div>

        <div class="pagination-buttons">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-item disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-item" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="page-item disabled" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-item active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-item">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-item" rel="next" aria-label="{{ __('pagination.next') }}">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-item disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
