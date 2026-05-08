@if ($paginator->hasPages())
    <nav class="pagination-wrap">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&laquo; Prev</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .pagination-wrap {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination-list {
            display: flex;
            list-style: none;
            padding: 0;
            gap: 5px;
        }
        .page-item .page-link {
            display: block;
            padding: 8px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            color: #374151;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
        }
        .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }
        .page-item:not(.disabled) .page-link:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: #fff5f0;
        }
        .page-item.active .page-link:hover {
            color: #fff;
            background: var(--accent);
        }
        .page-item.disabled .page-link {
            color: #9ca3af;
            background: #f9fafb;
            cursor: not-allowed;
        }
    </style>
@endif
